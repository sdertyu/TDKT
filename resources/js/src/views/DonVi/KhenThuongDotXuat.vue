<template>
    <div class="card m-4">
        <!-- Content Header -->

        <div class="card-header">
            <h1 class="card-title">Khen thưởng đột xuất</h1>
        </div>
        <!-- Main content -->
        <section class="content mt-3">
            <div class="container-fluid">
                <div class="row">
                    <!-- Khen thưởng cá nhân -->
                    <div class="col-md-7">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-award mr-2"></i>
                                    Đề xuất khen thưởng cá nhân
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Danh sách danh hiệu đã chọn -->
                                <div class="form-group">
                                    <label>Danh hiệu đề xuất</label>
                                    <multiselect v-model="selectedAwardsObjects" :options="individualAwards"
                                        :multiple="true" track-by="id" label="name" placeholder="Chọn danh hiệu"
                                        @input="handleSelectedAwardsChange"></multiselect>
                                    <small class="form-text text-muted">Chọn một hoặc nhiều danh hiệu</small>
                                </div>

                                <!-- Hiển thị form cho từng danh hiệu đã chọn -->
                                <div v-for="award in selectedAwardsObjects" :key="award.id"
                                    class="card card-outline card-info mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ award.name }}</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" @click="removeAward(award.id)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Chọn cá nhân cho danh hiệu này -->
                                        <div class="form-group">
                                            <label>Chọn cá nhân</label>
                                            <multiselect v-model="awardIndividuals[award.id]" :options="individuals"
                                                :multiple="true" track-by="id" label="displayName"
                                                placeholder="Chọn cá nhân" :custom-label="customLabel"
                                                :searchable="true" :internal-search="false"
                                                @search-change="searchIndividuals"></multiselect>
                                            <small class="form-text text-muted">Có thể chọn nhiều cá nhân, tìm kiếm
                                                theo mã hoặc tên</small>
                                        </div>

                                        <!-- Danh sách cá nhân đã chọn cho danh hiệu này -->
                                        <div v-if="awardIndividuals[award.id] && awardIndividuals[award.id].length > 0"
                                            class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10px">STT</th>
                                                        <th class="text-center">Mã NV</th>
                                                        <th class="text-center">Cá nhân</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(individual, index) in awardIndividuals[award.id]"
                                                        :key="individual.id">
                                                        <td class="text-center">{{ index + 1 }}</td>
                                                        <td>{{ individual.code }}</td>
                                                        <td>{{ individual.name }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Khen thưởng đơn vị -->
                    <div class="col-md-5">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-building mr-2"></i>
                                    Đề xuất khen thưởng đơn vị
                                </h3>
                            </div>
                            <div class="card-body">
                                <!-- Danh hiệu đề xuất cho đơn vị -->
                                <div class="form-group">
                                    <label>Danh hiệu đề xuất cho đơn vị</label>
                                    <multiselect v-model="selectedUnitAwards" :options="unitAwards" :multiple="true"
                                        track-by="id" label="name" placeholder="Chọn danh hiệu"></multiselect>
                                    <small class="form-text text-muted">Có thể chọn nhiều danh hiệu</small>
                                </div>

                                <!-- Danh sách danh hiệu đã chọn -->
                                <div class="form-group" v-if="selectedUnitAwards.length > 0">
                                    <label>Danh sách danh hiệu đã chọn</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10px">STT</th>
                                                    <th>Danh hiệu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(award, index) in selectedUnitAwards" :key="award.id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>{{ award.name }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unified action buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <button type="button" @click="handleSubmitAll" class="btn btn-lg btn-primary">
                                    <i class="fas fa-paper-plane mr-1"></i> Gửi đề xuất
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách đề xuất đã tạo -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Danh sách đề xuất đột xuất đã tạo
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" @click="refreshProposals">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div v-if="isLoading" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div v-else-if="proposals.length === 0" class="text-center">
                                    <p class="text-muted">Chưa có đề xuất nào được tạo</p>
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">STT</th>
                                                <th>Loại đề xuất</th>
                                                <th>Danh hiệu</th>
                                                <th>Đối tượng</th>
                                                <th>Ngày tạo</th>
                                                <th style="width: 120px">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(proposal, index) in proposals" :key="proposal.id">
                                                <td class="text-center">{{ index + 1 }}</td>
                                                <td>{{ proposal.tai_khoan.ca_nhan !== null ? 'Cá nhân' : 'Đơn vị' }}
                                                </td>
                                                <td>{{ proposal.danh_hieu.sTenDanhHieu }}</td>
                                                <td>{{ proposal.tai_khoan.ca_nhan !== null ?
                                                    proposal.tai_khoan.ca_nhan.sTenCaNhan :
                                                    proposal.tai_khoan.don_vi.sTenDonVi }}</td>
                                                <td>{{ formatDate(proposal.dNgayTao) }}</td>
                                                <td>
                                                    <div class="text-center">
                                                        <button @click="editProposal(proposal)"
                                                            class="btn btn-sm btn-warning me-2" data-bs-toggle="modal"
                                                            data-bs-target="#editProposalModal">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button @click="deleteProposal(proposal)"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
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
                </div>
            </div>
        </section>

        <!-- Edit Proposal Modal -->
        <div class="modal fade" id="editProposalModal" tabindex="-1" aria-labelledby="editProposalModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProposalModalLabel">Chỉnh sửa đề xuất</h5>
                        <button type="button" class="btn-close" @click="handleClose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="editingProposal">
                            <div class="mb-3">
                                <label class="form-label">Loại đề xuất:</label>
                                <div>{{ editingProposal.tai_khoan?.ca_nhan !== null ? 'Cá nhân' : 'Đơn vị' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Danh hiệu:</label>
                                <div>{{ editingProposal.danh_hieu?.sTenDanhHieu }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Đối tượng:</label>
                                <div>{{ editingProposal.tai_khoan?.ca_nhan !== null ?
                                    editingProposal.tai_khoan?.ca_nhan.sTenCaNhan :
                                    editingProposal.tai_khoan?.don_vi.sTenDonVi }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ngày tạo:</label>
                                <div>{{ formatDate(editingProposal.dNgayTao) }}</div>
                            </div>

                            <!-- Nếu là đề xuất cho cá nhân -->
                            <div v-if="editingProposal.tai_khoan?.ca_nhan !== null">
                                <div class="mb-3">
                                    <label class="form-label">Chọn cá nhân mới:</label>
                                    <multiselect v-model="editingProposalNewIndividual" :options="individuals"
                                        track-by="id" label="displayName" placeholder="Chọn cá nhân thay thế"
                                        :custom-label="customLabel" :searchable="true" :internal-search="false"
                                        @search-change="searchIndividuals">
                                    </multiselect>
                                </div>
                            </div>

                            <!-- Nếu là đề xuất cho đơn vị, có thể thêm các trường cần thiết ở đây -->
                            <div v-else>
                                <div class="mb-3">
                                    <label class="form-label">Chọn danh hiệu mới:</label>
                                    <multiselect v-model="editingProposalNewUnitAward" :options="unitAwards"
                                        track-by="id" label="name" placeholder="Chọn danh hiệu thay thế">
                                    </multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="handleClose">Đóng</button>
                        <button type="button" class="btn btn-primary" @click="saveEditedProposal">Lưu thay đổi</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { get } from 'jquery';
import Swal from 'sweetalert2';
import { ref, reactive, onMounted, computed } from 'vue';
import Multiselect from 'vue-multiselect';

// Danh sách cá nhân (giả lập dữ liệu)
const individuals = ref([]);

// Danh sách danh hiệu cá nhân
const individualAwards = ref([]);

// Danh sách danh hiệu đơn vị
const unitAwards = ref([]);

// Form state cho cá nhân
const selectedAwardsObjects = ref([]);
const selectedAwards = computed(() => selectedAwardsObjects.value.map(award => award.id));
const awardIndividuals = reactive({});
const awardReasons = reactive({});

// Form state cho đơn vị
const selectedUnitAwards = ref([]);
const unitReason = ref('');

// Custom label cho multiselect
const customLabel = (option) => {
    return `${option.code} - ${option.name}`;
};

// Tìm kiếm cá nhân
const searchIndividuals = (query) => {
    // Trong thực tế, đây sẽ là một API call
    // Ví dụ: axios.get(`/api/individuals?search=${query}`)
    console.log('Searching for:', query);
    // Mô phỏng tìm kiếm local
    if (!query) return;

    // Tìm kiếm theo cả mã nhân viên và tên
    const lowercaseQuery = query.toLowerCase();
    return individuals.value.filter(individual =>
        individual.code.toLowerCase().includes(lowercaseQuery) ||
        individual.name.toLowerCase().includes(lowercaseQuery)
    );
};

// Lấy tên cá nhân từ ID
const getIndividualName = (id) => {
    const individual = individuals.value.find(item => item.id === id);
    return individual ? individual.name : '';
};

// Lấy tên danh hiệu từ ID
const getAwardName = (id) => {
    const individualAward = individualAwards.value.find(item => item.id === id);
    if (individualAward) return individualAward.name;

    const unitAward = unitAwards.value.find(item => item.id === id);
    return unitAward ? unitAward.name : '';
};

// Xử lý khi danh sách danh hiệu thay đổi
const handleSelectedAwardsChange = () => {
    // Khởi tạo dữ liệu cho các danh hiệu mới được chọn
    selectedAwardsObjects.value.forEach(award => {
        if (!awardIndividuals[award.id]) {
            awardIndividuals[award.id] = [];
        }
        if (!awardReasons[award.id]) {
            awardReasons[award.id] = '';
        }
    });

    // Xóa dữ liệu của các danh hiệu đã bỏ chọn
    Object.keys(awardIndividuals).forEach(awardId => {
        if (!selectedAwardsObjects.value.some(award => award.id === awardId)) {
            delete awardIndividuals[awardId];
            delete awardReasons[awardId];
        }
    });
};

// Xóa danh hiệu
const removeAward = (awardId) => {
    selectedAwardsObjects.value = selectedAwardsObjects.value.filter(award => award.id !== awardId);
    delete awardIndividuals[awardId];
    delete awardReasons[awardId];
};

// Xử lý gửi form đơn vị
const handleSubmitUnit = () => {
    // Kiểm tra dữ liệu
    if (selectedUnitAwards.value.length === 0) {
        alert('Vui lòng chọn ít nhất một danh hiệu');
        return;
    }

    // Chuẩn bị dữ liệu gửi đi
    const data = {
        awards: selectedUnitAwards.value,
        reason: unitReason.value
    };

    console.log('Gửi đề xuất khen thưởng đơn vị:', data);
    // Gọi API để gửi dữ liệu
    // axios.post('/api/khen-thuong/don-vi', data)...

    alert('Đã gửi đề xuất khen thưởng đơn vị thành công!');
    handleResetUnit();
};


// Reset form đơn vị
const handleResetUnit = () => {
    selectedUnitAwards.value = [];
    unitReason.value = '';
};

const getListDanhHieuDotXuat = async () => {
    const list = await axios.get('/api/danhhieu/listdanhhieudotxuat', {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (list.status === 200) {
        individualAwards.value = list.data.data.caNhan.map(item => ({
            id: item.PK_MaDanhHieu,
            name: item.sTenDanhHieu
        }));
        unitAwards.value = list.data.data.donVi.map(item => ({
            id: item.PK_MaDanhHieu,
            name: item.sTenDanhHieu
        }));
    } else {
        console.error('Lỗi khi lấy danh hiệu đột xuất:', list.statusText);
    }
}

const getCaNhanTrongDonVi = async () => {
    // console.log(sessionStorage);
    const response = await axios.get(`api/taikhoan/caNhanTrongDonVi/${sessionStorage.getItem('maDonVi')}`, {
        headers: {
            Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
        }
    });

    if (response.status === 200) {
        const data = response.data.data;
        individuals.value = data.map(item => ({
            id: item.FK_MaTaiKhoan,
            code: item.PK_MaCaNhan,
            name: item.sTenCaNhan,
            displayName: `${item.PK_MaCaNhan} - ${item.sTenCaNhan}`
        }));
    }
    else {
        console.error('Lỗi khi lấy danh sách cá nhân:', response);
    }
}

// Xử lý gửi cả hai form cùng lúc
const handleSubmitAll = async () => {
    const hasIndividualAwards = selectedAwardsObjects.value.length > 0;
    const hasUnitAwards = selectedUnitAwards.value.length > 0;

    // Kiểm tra xem có bất kỳ đề xuất nào không
    if (!hasIndividualAwards && !hasUnitAwards) {
        alert('Vui lòng chọn ít nhất một danh hiệu cho cá nhân hoặc đơn vị');
        return;
    }

    let isValid = true;
    const individualNominations = [];
    let unitNomination = null;

    // Xử lý và kiểm tra dữ liệu cho khen thưởng cá nhân
    if (hasIndividualAwards) {
        selectedAwardsObjects.value.forEach(award => {
            if (!awardIndividuals[award.id] || awardIndividuals[award.id].length === 0) {
                alert(`Vui lòng chọn ít nhất một cá nhân cho danh hiệu "${award.name}"`);
                isValid = false;
                return;
            }

            individualNominations.push({
                award: award,
                individuals: awardIndividuals[award.id],
                // reason: awardReasons[award.id]
            });
        });
    }

    // Xử lý dữ liệu cho khen thưởng đơn vị
    if (hasUnitAwards) {
        unitNomination = {
            awards: selectedUnitAwards.value,
            reason: unitReason.value
        };
    }

    if (!isValid) return;

    try {
        // Gửi đề xuất khen thưởng cá nhân nếu có
        if (hasIndividualAwards || hasUnitAwards) {
            const individualResponse = await axios.post('/api/dexuat/themdexuatdotxuat', {
                caNhan: individualNominations.map(nomination => ({
                    danhHieu: nomination.award.id,
                    caNhan: nomination.individuals.map(individual => individual.id),
                })),
                donVi: selectedUnitAwards.value,
            }, {
                headers: {
                    Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                }
            });

            if (individualResponse.status !== 200) {
                throw new Error('Lỗi khi gửi đề xuất khen thưởng cá nhân');
            }
        }

        // Thông báo thành công nếu mọi thứ đều ổn
        toastSuccess('Đã gửi đề xuất khen thưởng thành công!');

    } catch (error) {
        // console.log(error);
        if (error.response.status === 422) {
            const errors = error.response.data.error;
            let errorMessage = Object.values(errors).flat().join('<br>');
            toastError(errorMessage);
        } else {
            toastError(error.response.data.message || 'Có lỗi xảy ra khi lưu đợt thi đua đột xuất');
        }
    }
};

// Thông tin về đề xuất đã tạo
const proposals = ref([]);
const isLoading = ref(false);

// Format date function
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(date);
};

// Get tooltip text for multiple individuals
const getTooltipNames = (individuals) => {
    if (!individuals || individuals.length === 0) return '';
    return individuals.map(ind => ind.tenDoiTuong).join(', ');
};

// Fetch all proposals
const fetchProposals = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/dexuat/thongtindexuatdotxuat', {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            proposals.value = response.data.data.dotXuat || [];
        }
    } catch (error) {
        console.error('Lỗi khi lấy danh sách đề xuất:', error);
        // toastError('Không thể lấy danh sách đề xuất');
    } finally {
        isLoading.value = false;
    }
};

// Refresh proposals list
const refreshProposals = () => {
    fetchProposals();
};

// For editing proposals in modal
const editingProposal = ref(null);
const editingProposalNewIndividual = ref(null);
const editingProposalNewUnitAward = ref(null);

// Edit proposal
const editProposal = (proposal) => {
    // Store the current proposal for editing
    editingProposal.value = JSON.parse(JSON.stringify(proposal));
    editingProposalNewIndividual.value = null;
    editingProposalNewUnitAward.value = null;

    // Use Bootstrap's modal method to show the modal
    // If you're using Bootstrap 5 with vanilla JS
    const modalEl = document.getElementById('editProposalModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
};

// Save edited proposal
const saveEditedProposal = async () => {
    try {
        if (!editingProposal.value) return;

        let payload = {
            maDeXuat: editingProposal.value.PK_MaDeXuat
        };

        // Kiểm tra loại đề xuất và dữ liệu tương ứng
        if (editingProposal.value.tai_khoan?.ca_nhan !== null) {
            // Đề xuất cho cá nhân
            if (!editingProposalNewIndividual.value) {
                toastError('Vui lòng chọn cá nhân thay thế');
                return;
            }
            payload.maTaiKhoanMoi = editingProposalNewIndividual.value?.id;
        } else {
            // Đề xuất cho đơn vị
            if (!editingProposalNewUnitAward.value) {
                toastError('Vui lòng chọn danh hiệu thay thế');
                return;
            }
            payload.maDanhHieuMoi = editingProposalNewUnitAward.value?.id;
        }

        const response = await axios.put('/api/dexuat/capnhatdexuatdotxuat', payload, {
            headers: {
                Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            toastSuccess('Cập nhật đề xuất thành công');

            // Close modal - Using Bootstrap 5 method
            handleClose();

            // Refresh the proposals list
            fetchProposals();
        }
    } catch (error) {
        console.error('Lỗi khi cập nhật đề xuất:', error);
        toastError(error.response?.data?.message || 'Không thể cập nhật đề xuất');
    }
};

// Delete proposal
const deleteProposal = async (proposal) => {
    Swal.fire({
        title: 'Bạn có chắc chắn muốn xóa đề xuất này?',
        text: "Bạn sẽ không thể khôi phục lại đề xuất này!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xóa'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/dexuat/xoadexuatdotxuat/${proposal.PK_MaDeXuat}`, {
                    headers: {
                        Authorization: `Bearer ${sessionStorage.getItem('api_token')}`
                    }
                });

                if (response.status === 200) {
                    toastSuccess('Đã xóa đề xuất thành công');
                    fetchProposals(); // Refresh the list
                }
            } catch (error) {
                console.error('Lỗi khi xóa đề xuất:', error);
                toastError(error.response?.data?.message || 'Không thể xóa đề xuất');
            }
        }
    });


};

// Khởi tạo sau khi component được mount
onMounted(() => {
    // getThongTinDeXuatDotXuat();
    getListDanhHieuDotXuat();
    getCaNhanTrongDonVi();
    fetchProposals(); // Fetch existing proposals
});

const handleClose = () => {
    // Đầu tiên ẩn modal bằng Bootstrap API
    const modalEl = document.getElementById('editProposalModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Sau đó, chờ một chút và dọn dẹp backdrop
    setTimeout(() => {
        document.querySelector('.modal-backdrop').remove();
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }, 150);
}
</script>

<style scoped>
.card {
    margin-bottom: 1rem;
}

.table-responsive {
    overflow-x: auto;
}

/* Add any modal specific styles here */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}
</style>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
