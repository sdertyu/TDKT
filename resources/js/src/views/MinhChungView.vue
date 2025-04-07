<template>
    <div class="container py-4">
        <!-- Main Card -->
        <div class="card border-0 shadow">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-cloud-upload me-2"></i>Upload Minh Chứng
                    </h4>
                    <button class="btn btn-sm btn-light" @click="showUploadModal = true">
                        <i class="bi bi-plus-lg me-1"></i>Thêm mới
                    </button>
                </div>

            </div>


            <div class="card-body p-4">
                <!-- Empty state -->
                <div v-if="files.length === 0" class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-folder2-open text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-3">Chưa có file nào</h5>
                </div>

                <!-- File list -->
                <div v-else>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th style="width: 45%" class="text-center">Tên file</th>
                                    <th class="text-center">Ngày tải lên</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(file, index) in files" :key="index" class="border-bottom">
                                    <td class="text-center">{{ index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="file-icon me-3">
                                                <i :class="getFileIconClass(file.sTenFile)" class="bi fs-3"></i>
                                            </div>
                                            <div class="file-info">
                                                <div class="fw-medium text-truncate" style="max-width: 280px;">{{
                                                    file.sTenFile }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ file.dNgayTao }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" title="Tải xuống"
                                                @click="downloadFile(file)">
                                                <i class="bi bi-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" @click="removeFile(file)"
                                                title="Xóa file">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" :class="{ 'show d-block': showUploadModal }" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-cloud-upload me-2"></i>Tải lên file mới
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="showUploadModal = false"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="upload-area p-4 rounded-3 border border-2 border-dashed"
                            :class="{ 'border-primary bg-light': isDragging }" @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="onFileChange($event); isDragging = false">
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="mb-3">Kéo và thả file vào đây</h5>
                                <p class="text-muted mb-4">Hoặc nhấp vào nút bên dưới để chọn file từ máy tính của bạn
                                </p>
                                <label for="file-upload" class="btn btn-primary btn-lg">
                                    <i class="bi bi-folder-plus me-2"></i>Chọn file
                                </label>
                                <input id="file-upload" type="file" multiple @change="onFileChange" class="d-none" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Backdrop -->
        <div v-if="showUploadModal" class="modal-backdrop fade show"></div>

        <!-- Upload Progress Toast (góc phải dưới màn hình) -->
        <div v-if="uploadingFiles.length > 0" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
            <div class="toast show bg-white shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-primary text-white">
                    <i class="bi bi-arrow-up-circle me-2"></i>
                    <strong class="me-auto">Tiến trình tải lên</strong>
                    <small>{{ uploadingFiles.length }} file</small>
                    <button type="button" class="btn-close btn-close-white" @click="dismissToast"></button>
                </div>
                <div class="toast-body p-3">
                    <div v-for="(file, index) in uploadingFiles" :key="'toast-upload-' + index" class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-truncate" style="max-width: 200px;">{{ file.name }}</span>
                            <span class="ms-2 fw-medium">{{ file.progress }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                :style="{ width: file.progress + '%' }"
                                :class="{ 'bg-success': file.progress === 100 }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Swal from 'sweetalert2';
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';


// State variables
const files = ref([]);
const uploadingFiles = ref([]);
const isDragging = ref(false);
const showUploadModal = ref(false);
const showToast = ref(true);
const maDeXuat = useRoute().params.id;

onMounted(() => {
    getListMinhChung()
})

const getListMinhChung = () => {
    axios.get(`/api/minhchung/getlist/${maDeXuat}`, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                files.value = response.data.data;
                console.log(files.value);
            }
        })
        .catch(error => {
            toastError('Không thể tải danh sách minh chứng');
            console.error(error);
        });
};

// Methods
const onFileChange = async (event) => {
    const files = event.dataTransfer?.files || event.target?.files;

    if (!files || files.length === 0) return;

    const formData = new FormData();
    Array.from(files).forEach(file => {
        formData.append('files[]', file);
    });

    formData.append('madexuat', maDeXuat);

    try {
        const response = await axios.post('/api/minhchung/upload', formData, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`,
            },
            onUploadProgress: (progressEvent) => {
                const percent = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                uploadingFiles.value = Array.from(files).map(file => ({
                    name: file.name,
                    progress: percent,
                }));
            }
        });

        if (response.status === 200) {
            toastSuccess('Lưu minh chứng thành công');
        }
    } catch (error) {
        toastError('Lưu minh chứng thất bại');
    } finally {
        showUploadModal.value = false;
    }
};

const downloadFile = (file) => {
    axios.get('/api/minhchung/download/' + file.PK_MaMinhChung, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        },
        responseType: 'blob' // bắt buộc để nhận file binary
    })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]))
            const link = document.createElement('a')
            link.href = url
            link.setAttribute('download', file.sTenFile) // tên file gốc
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
            window.URL.revokeObjectURL(url) // dọn rác
        })
        .catch(error => {
            console.error('Tải file thất bại:', error)
        })
}

const removeFile = (file) => {
    Swal.fire({
        title: 'Xóa minh chứng',
        text: 'Bạn có chắc muốn xóa minh chứng này không?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
    }).then((result) => {
        if (!result.isConfirmed) return;

        axios.post(`/api/minhchung/delete/${file.PK_MaMinhChung}`, null, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        })
            .then((response) => {
                if (response.status === 200) {
                    toastSuccess('Xóa minh chứng thành công');
                    getListMinhChung();
                }
            })
            .catch((error) => {
                console.error(error);
                toastError('Xóa minh chứng thất bại');
            });
    });
};


const dismissToast = () => {
    // Chỉ ẩn toast, không xóa dữ liệu uploadingFiles
    showToast.value = false;
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

const getFileType = (filename) => {
    const extension = filename.split('.').pop().toLowerCase();
    const typeMap = {
        'jpg': 'image/jpeg',
        'jpeg': 'image/jpeg',
        'png': 'image/png',
        'gif': 'image/gif',
        'pdf': 'application/pdf',
        'doc': 'application/msword',
        'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls': 'application/vnd.ms-excel',
        'xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'zip': 'application/zip',
        'rar': 'application/x-rar-compressed',
        '7z': 'application/x-7z-compressed',
        'mp3': 'audio/mpeg',
        'mp4': 'video/mp4'
    };

    return typeMap[extension] || 'application/octet-stream';
};

const getFileTypeLabel = (type) => {
    if (type.includes('image')) {
        return 'Hình ảnh';
    } else if (type.includes('pdf')) {
        return 'PDF Document';
    } else if (type.includes('word') || type.includes('document')) {
        return 'Word Document';
    } else if (type.includes('excel') || type.includes('sheet')) {
        return 'Excel Spreadsheet';
    } else if (type.includes('zip') || type.includes('rar') || type.includes('7z')) {
        return 'Archived File';
    } else if (type.includes('audio')) {
        return 'Audio File';
    } else if (type.includes('video')) {
        return 'Video File';
    } else {
        return 'Document';
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatDate = (date) => {
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const simulateUpload = (fileIndex) => {
    const file = files.value[fileIndex];

    // Add to uploading files
    uploadingFiles.value.push({
        name: file.name,
        progress: 0
    });

    const uploadingIndex = uploadingFiles.value.length - 1;

    // Update file status
    files.value[fileIndex].status = 'uploading';

    const intervalId = setInterval(() => {
        if (uploadingFiles.value[uploadingIndex]) {
            uploadingFiles.value[uploadingIndex].progress += 5;

            if (uploadingFiles.value[uploadingIndex].progress >= 100) {
                clearInterval(intervalId);

                // Update file status to success
                files.value[fileIndex].status = 'success';

                // Remove from uploading after a delay
                setTimeout(() => {
                    const index = uploadingFiles.value.findIndex(f => f.name === file.name);
                    if (index !== -1) {
                        uploadingFiles.value.splice(index, 1);
                    }
                }, 2000);
            }
        } else {
            clearInterval(intervalId);
        }
    }, 200);
};
</script>

<style scoped>
/* Custom styles to enhance Bootstrap */
.upload-area {
    transition: all 0.3s ease;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.upload-area:hover,
.upload-area.border-primary {
    background-color: #e9ecef;
}

.file-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Bootstrap Modal animation */
.modal.fade.show {
    transition: opacity 0.2s linear;
}

.modal-backdrop.fade.show {
    opacity: 0.5;
}

/* Toast styling */
.toast {
    max-width: 350px;
    border-radius: 8px;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.toast-header {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}
</style>