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
                            <th class="text-center">STT</th>
                            <th>Tên văn bản</th>
                            <th>Tên file</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="align-middle" v-for="(item, index) in listVanBanDinhKem" :key="item.id" @click="xemVBDK(item)">
                            <td class="text-center">{{ index + 1 }}</td>
                            <td>{{ item.sTenVanBan }}</td>
                            <div class="d-flex align-items-center">
                                <div class="file-icon me-3">
                                    <i :class="getFileIconClass(item.sTenFile)" class="bi fs-3"></i>
                                </div>
                                <div class="file-info">
                                    <div class="fw-medium text-truncate" style="max-width: 280px;">{{
                                        item.sTenFile }}</div>
                                </div>
                            </div>
                            <td class="text-center">
                                <button class="btn" @click.stop="downLoadFile(item)">
                                    <i class="bi bi-download"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade h-100" id="pdfPreviewModal" tabindex="-1" ref="pdfModalEl" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <iframe v-if="pdfUrl" :src="pdfUrl" width="100%" height="700px" style="border: none;" />
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
// import axios from "axios";
import Swal from "sweetalert2";

import { ref, onMounted, nextTick } from "vue";
// import { Modal } from "bootstrap";
const madot = ref(null);
const pdfUrl = ref('')

const listVanBanDinhKem = ref([]);

const getListVanBanDinhKem = () => {
    const listVB = axios.get("/api/dotthiduakhenthuong/list-van-ban-active", {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
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

const getFileIconClass = (fileName) => {
    if (!fileName) return 'bi-file-earmark text-secondary';

    const ext = fileName.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
        return 'bi-file-earmark-image text-info';
    } else if (ext === 'pdf') {
        return 'bi-file-earmark-pdf text-danger';
    } else if (['doc', 'docx'].includes(ext)) {
        return 'bi-file-earmark-word text-primary';
    } else if (['xls', 'xlsx'].includes(ext)) {
        return 'bi-file-earmark-excel text-success';
    } else if (['zip', 'rar', '7z'].includes(ext)) {
        return 'bi-file-earmark-zip text-warning';
    } else if (['mp3', 'wav', 'flac'].includes(ext)) {
        return 'bi-file-earmark-music text-success';
    } else if (['mp4', 'mov', 'avi', 'mkv'].includes(ext)) {
        return 'bi-file-earmark-play text-danger';
    } else {
        return 'bi-file-earmark text-secondary';
    }
};

onMounted(() => {
    getListVanBanDinhKem();
    madot.value = useGlobalStore().dotActive;
});

const xemVBDK = async (file) => {
    try {
        const response = await axios.get('/api/dotthiduakhenthuong/previewVbdk/' + file.PK_MaVanBan, {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            },
            responseType: 'blob'
        })

        const blob = new Blob([response.data], { type: 'application/pdf' })
        pdfUrl.value = URL.createObjectURL(blob)
        const modalEl = document.getElementById('pdfPreviewModal')
        if (modalEl) {
            const instance = bootstrap.Modal.getOrCreateInstance(modalEl)
            instance.show()
        }
    } catch (error) {
        console.error('Lỗi khi lấy PDF:', error)
    }
}

const downLoadFile = async (item) => {
    try {
        const response = await axios.get(
            `/api/dotthiduakhenthuong/downloadVbdk/${item.PK_MaVanBan}`,
            {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem("api_token")}`,
                },
                responseType: "blob",
            }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', item.sTenFile) // tên file gốc
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url) // dọn rác

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
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
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

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: white;
    width: 90%;
    max-width: 1000px;
    height: 90%;
    margin: 2rem auto;
    padding: 1rem;
    border-radius: 8px;
    overflow: hidden;
}

.modal-dialog {
    width: 95vw !important;
    height: 95vh;
}

.modal-content {
    height: 100%;
}

.modal-body {
    height: 100%;
    padding: 0;
}

.modal-body iframe {
    width: 100%;
    height: 100%;
    border: none;
}
</style>