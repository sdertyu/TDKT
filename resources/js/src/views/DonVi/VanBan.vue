<template>
    <div class="card my-3">
        <div class="card-header">
            <h3 class="card-title">Danh sách văn bản đính kèm năm học {{ madot }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" @click="downloadAllAsZip(madot)">
                    Tải xuống toàn bộ <i class="bi bi-download"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên văn bản</th>
                            <th>Tên file</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in listVanBanDinhKem" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.sTenVanBan }}</td>
                            <td>{{ item.sTenFile }}</td>
                            <td class="text-center">
                                <button class="btn" @click="downLoadFile(item)">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="docx-viewer">
            <div v-if="loading" class="loading">Đang tải tài liệu...</div>
            <div v-if="error" class="error">{{ error }}</div>
            <div v-if="!loading && !error" ref="docxContent" class="docx-content"></div>
        </div>


    </div>
</template>

<script setup>
// import axios from "axios";
import Swal from "sweetalert2";

import { ref, onMounted } from "vue";
const madot = ref(null);



const listVanBanDinhKem = ref([]);

const getListVanBanDinhKem = () => {
    const listVB = axios.get("/api/dotthiduakhenthuong/list-van-ban-active", {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("api_token")}`,
        },
    });

    listVB
        .then((response) => {
            if (response.status === 200) {
                if (response.data.data.length > 0) {
                    listVanBanDinhKem.value = response.data.data;
                }
                else {
                    listVanBanDinhKem.value = [];
                    Swal.fire({
                        toast: true,
                        icon: "warning",
                        title: "Chưa có văn bản đính kèm nào!",
                        position: "top-end",
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }
            }

        })
        .catch((error) => {
            console.log(error);
        });
};

onMounted(() => {
    getListVanBanDinhKem();
    madot.value = useGlobalStore().dotActive;
});

const downLoadFile = async (item) => {
    try {
        const response = await axios.get(
            `/api/dotthiduakhenthuong/previewVbdk/${item.PK_MaVanBan}`,
            {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("api_token")}`,
                },
                responseType: "blob",
            }
        );

        const blobUrl = URL.createObjectURL(response.data);
        window.open(blobUrl, '_blank');


        // const arrayBuffer = await response.data.arrayBuffer();

        // mammoth.convertToHtml({ arrayBuffer })
        //     .then(result => {
        //         docxHtml.value = result.value;
        //     })
        //     .catch(err => {
        //         console.error("Lỗi khi đọc file .docx:", err);
        //     });




        // const blob = new Blob([response.data]);
        // const url = window.URL.createObjectURL(blob);

        // const link = document.createElement('a');
        // link.href = url;

        // const fileName = getFileNameFromHeader(response.headers['content-disposition']) || 'download.docx';
        // link.download = fileName;

        // document.body.appendChild(link);
        // link.click();
        // document.body.removeChild(link);
        // window.URL.revokeObjectURL(url);


    } catch (error) {
        console.error("Lỗi khi tải file:", error);
    }
};

const getFileNameFromHeader = (disposition) => {
    if (!disposition) return null;

    const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
    if (match && match[1]) {
        return match[1].replace(/['"]/g, '');
    }
    return null;
}

const downloadAllAsZip = async () => {
    try {
        const response = await axios.get(`/api/dotthiduakhenthuong/downloadZip/${madot.value}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            },
            responseType: 'blob'
        });

        const blob = new Blob([response.data], { type: 'application/zip' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = madot.value + '.zip';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Lỗi khi tải ZIP:', error);
    }
};

</script>
