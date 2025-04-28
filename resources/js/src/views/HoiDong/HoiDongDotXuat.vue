<template>
    <div class="container-fluid mt-4">

        <!-- Danh sách cuộc họp đột xuất -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Danh sách cuộc họp hội đồng đột xuất</h4>
                <button class="btn btn-primary" @click="openAddModal">
                    <i class="fas fa-plus mr-1"></i> Thêm cuộc họp
                </button>
            </div>
            <div class="card-body">
                <div v-if="isLoading" class="text-center my-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Đang tải...</span>
                    </div>
                </div>
                <div v-else-if="danhSachHoiDong.length === 0" class="text-center my-4">
                    <p class="text-muted">Chưa có cuộc họp nào được tạo</p>
                    <button class="btn btn-outline-primary" @click="openAddModal">
                        <i class="fas fa-plus mr-1"></i> Thêm cuộc họp đầu tiên
                    </button>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">STT</th>
                                <th>Hướng dẫn số</th>
                                <th>Thời gian</th>
                                <th>Địa điểm</th>
                                <th>Mã kiện toàn</th>
                                <!-- <th>Tỷ lệ tham dự</th> -->
                                <th style="width: 180px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in danhSachHoiDong" :key="item.PK_MaHoiDong">
                                <td class="text-center">{{ index + 1 }}</td>
                                <td>{{ item.sSoHD }}</td>
                                <td>{{ formatDateTime(item.dThoiGianHop) }}</td>
                                <td>{{ item.sDiaChi }}</td>
                                <td>{{ item.FK_MaKienToan }}</td>
                                <!-- <td>
                                    {{ item.iSoNguoiThamDu }}/{{ item.iSoThanhVien }}
                                    ({{ calculateAttendanceRate(item.iSoNguoiThamDu, item.iSoThanhVien) }}%)
                                </td> -->
                                <td>
                                    <div class="text-center">
                                        <button class="btn btn-sm btn-info me-1" @click="viewHoiDong(item)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning me-1" @click="editHoiDong(item)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" @click="deleteHoiDong(item)">
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

        <!-- Modal Thêm/Sửa Hội đồng -->
        <div class="modal fade" id="hoiDongModal" tabindex="-1" role="dialog" aria-labelledby="hoiDongModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="hoiDongModalLabel">
                            {{ isEditing ? 'Cập nhật thông tin cuộc họp' : 'Thêm mới cuộc họp' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveHoiDong">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Thông tin cuộc họp</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Theo hướng dẫn số</label>
                                                <input v-model="hoiDong.huongDanSo" type="text" class="form-control"
                                                    placeholder="Nhập số hướng dẫn" required />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Thời gian</label>
                                                <input v-model="hoiDong.thoiGian" type="datetime-local"
                                                    class="form-control" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Địa chỉ</label>
                                                <input v-model="hoiDong.diaChi" type="text" class="form-control"
                                                    placeholder="Nhập địa chỉ" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Tổng số người triệu tập</label>
                                                <input v-model.number="hoiDong.tongNguoiTrieuTap" type="number" min="1"
                                                    class="form-control" required />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Số người có mặt</label>
                                                <input v-model.number="hoiDong.soNguoiCoMat" type="number" min="1"
                                                    class="form-control" required />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Số người vắng</label>
                                                <input type="text" class="form-control" readonly disabled
                                                    v-model="hoiDong.soVang" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Mã quyết định kiện toàn</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Mã quyết định kiện toàn" v-model="hoiDong.maKienToan"
                                                    readonly disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Chủ tịch hội đồng</label>
                                                <select v-model="hoiDong.chuTichId" class="form-select" required>
                                                    <option value="">-- Chọn chủ tịch hội đồng --</option>
                                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                                        {{ canhan.sTenCaNhan }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Phó chủ tịch thường trực hội đồng</label>
                                                <select v-model="hoiDong.phoThuongTrucId" class="form-select">
                                                    <option value="">-- Chọn phó chủ tịch thường trực --</option>
                                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                                        {{ canhan.sTenCaNhan }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Phó chủ tịch hội đồng</label>
                                                <select v-model="hoiDong.phoChuTichId" class="form-select">
                                                    <option value="">-- Chọn phó chủ tịch hội đồng --</option>
                                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                                        {{ canhan.sTenCaNhan }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Thư ký</label>
                                                <select v-model="hoiDong.thuKyId" class="form-select" required>
                                                    <option value="">-- Chọn thư ký --</option>
                                                    <option v-for="canhan in danhSachCanhan" :key="canhan.taikhoan.PK_MaTaiKhoan"
                                                        :value="canhan.taikhoan.PK_MaTaiKhoan">
                                                        {{ canhan.sTenCaNhan }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> -->

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Tệp đính kèm biên bản họp</label>
                                                <input type="file" class="form-control"
                                                    @change="e => handleFileUpload('bienBan', e)"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                                <small class="text-muted">Hỗ trợ file: PDF, Word, Excel, hình ảnh (tối
                                                    đa 10MB)</small>
                                                <p v-if="hoiDong.tenBienBan" class="mt-2 fst-italic">
                                                    <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{
                                                        hoiDong.tenBienBan }}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Tệp đính kèm biên bản kiểm phiếu</label>
                                                <input type="file" class="form-control"
                                                    @change="e => handleFileUpload('kiemPhieu', e)"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" />
                                                <small class="text-muted">Hỗ trợ file: PDF, Word, Excel, hình ảnh (tối
                                                    đa 10MB)</small>
                                                <p v-if="hoiDong.tenKiemPhieu" class="mt-2 fst-italic">
                                                    <i class="fas fa-file-alt me-1"></i> File đã tải lên: <strong>{{
                                                        hoiDong.tenKiemPhieu }}</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Ghi chú</label>
                                                <textarea v-model="hoiDong.ghiChu" class="form-control" rows="3"
                                                    placeholder="Nhập ghi chú"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-primary" @click="saveHoiDong">
                                            {{ isEditing ? 'Cập nhật' : 'Lưu' }} thông tin hội đồng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Danh sách đề xuất section (Only shown when editing) -->
                        <div>
                            <!-- Đề xuất cá nhân -->
                            <div class="card mt-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Danh sách đề xuất cá nhân</h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-success me-2"
                                            @click="setAllCaNhanStatus('1')">
                                            <i class="fas fa-check me-1"></i> Đạt
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            @click="setAllCaNhanStatus('0')">
                                            <i class="fas fa-times me-1"></i> Không đạt
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px">STT</th>
                                                    <th>Tên danh hiệu</th>
                                                    <th>Người đề xuất</th>
                                                    <th>Đơn vị</th>
                                                    <th style="width: 150px">Tổng số người bầu</th>
                                                    <th style="width: 100px">Tỷ lệ %</th>
                                                    <th style="width: 150px">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(deXuat, index) in danhSachDeXuatCaNhan"
                                                    :key="deXuat.PK_MaDeXuat">
                                                    <td class="text-center">{{ index + 1 }}</td>
                                                    <td>{{ deXuat.danh_hieu }}</td>
                                                    <td>{{ deXuat.ca_nhan.ten_ca_nhan }}</td>
                                                    <td>{{ deXuat.ca_nhan.don_vi }}</td>
                                                    <td>
                                                        <input v-model.number="deXuat.so_nguoi_bau" type="number"
                                                            min="0" class="form-control" :max="hoiDong.soNguoiCoMat" />
                                                    </td>
                                                    <td class="text-center fw-bold">
                                                        {{ hoiDong.soNguoiCoMat > 0 ? ((deXuat.so_nguoi_bau /
                                                            hoiDong.soNguoiCoMat) *
                                                        100).toFixed(2) : 0 }}%
                                                    </td>
                                                    <td>
                                                        <select v-model="deXuat.trang_thai" class="form-select">
                                                            <option value="1">Đạt</option>
                                                            <option value="0">Không đạt</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr v-if="danhSachDeXuatCaNhan.length === 0">
                                                    <td colspan="7" class="text-center">Không có đề xuất cá nhân nào
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Đề xuất đơn vị -->
                            <div class="card mt-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Danh sách đề xuất đơn vị</h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-success me-2"
                                            @click="setAllDonViStatus('1')">
                                            <i class="fas fa-check me-1"></i> Đạt
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            @click="setAllDonViStatus('0')">
                                            <i class="fas fa-times me-1"></i> Không đạt
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px">STT</th>
                                                    <th>Tên danh hiệu</th>
                                                    <th>Đơn vị</th>
                                                    <th style="width: 150px">Tổng số người bầu</th>
                                                    <th style="width: 100px">Tỷ lệ %</th>
                                                    <th style="width: 150px">Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(deXuat, index) in danhSachDeXuatDonVi"
                                                    :key="deXuat.PK_MaDeXuat">
                                                    <td class="text-center">{{ index + 1 }}</td>
                                                    <td>{{ deXuat.danh_hieu }}</td>
                                                    <td>{{ deXuat.don_vi.ten_don_vi }}</td>
                                                    <td>
                                                        <input v-model.number="deXuat.so_nguoi_bau" type="number"
                                                            min="0" class="form-control" :max="hoiDong.soNguoiCoMat" />
                                                    </td>
                                                    <td class="text-center fw-bold">
                                                        {{ hoiDong.soNguoiCoMat > 0 ? ((deXuat.so_nguoi_bau /
                                                            hoiDong.soNguoiCoMat) *
                                                        100).toFixed(2) : 0 }}%
                                                    </td>
                                                    <td>
                                                        <select v-model="deXuat.trang_thai" class="form-select">
                                                            <option value="1">Đạt</option>
                                                            <option value="0">Không đạt</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr v-if="danhSachDeXuatDonVi.length === 0">
                                                    <td colspan="6" class="text-center">Không có đề xuất đơn vị nào</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                                        <button type="button" class="btn btn-success" @click="saveDeXuat">
                                            Lưu danh sách đề xuất
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xem chi tiết -->
        <div class="modal fade" id="viewHoiDongModal" tabindex="-1" role="dialog"
            aria-labelledby="viewHoiDongModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewHoiDongModalLabel">Chi tiết cuộc họp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedHoiDong">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Hướng dẫn số:</strong> {{ selectedHoiDong.sSoHD }}</p>
                                    <p><strong>Thời gian:</strong> {{ formatDateTime(selectedHoiDong.dThoiGianHop) }}
                                    </p>
                                    <p><strong>Địa chỉ:</strong> {{ selectedHoiDong.sDiaChi }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Số người triệu tập: </strong> {{ selectedHoiDong.kien_toan.thanh_vien_hoi_dong_count }}</p>
                                    <p><strong>Mã kiện toàn: </strong> {{ selectedHoiDong.FK_MaKienToan }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <p><strong>Ghi chú:</strong> {{ selectedHoiDong.sGhiChu || 'Không có' }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0">Biên bản họp</h6>
                                        </div>
                                        <div class="card-body">
                                            <p v-if="selectedHoiDong.sTenBienBan">
                                                <i class="fas fa-file-alt me-2"></i>
                                                <a href="#" @click.prevent="downloadFile(selectedHoiDong.sTenBienBan)">
                                                    {{ selectedHoiDong.sTenBienBan }}
                                                </a>
                                            </p>
                                            <p v-else class="text-muted">Chưa có biên bản</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="card-title mb-0">Biên bản kiểm phiếu</h6>
                                        </div>
                                        <div class="card-body">
                                            <p v-if="selectedHoiDong.sTenKiemPhieu">
                                                <i class="fas fa-file-alt me-2"></i>
                                                <a href="#"
                                                    @click.prevent="downloadFile(selectedHoiDong.sTenKiemPhieu)">
                                                    {{ selectedHoiDong.sTenKiemPhieu }}
                                                </a>
                                            </p>
                                            <p v-else class="text-muted">Chưa có biên bản kiểm phiếu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-info" @click="editSelectedHoiDong">
                            <i class="fas fa-edit me-1"></i> Chỉnh sửa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { get } from 'jquery';
import { ref, onMounted, watch } from 'vue';
import Swal from 'sweetalert2';
// import axios from 'axios';

// Danh sách hội đồng
const danhSachHoiDong = ref([]);
const isLoading = ref(true);
const isEditing = ref(false);
const selectedHoiDong = ref(null);
const showDeXuatSection = ref(false);

// Hội đồng data structure
const hoiDong = ref({
    maHoiDong: null,
    huongDanSo: '',
    thoiGian: '',
    diaChi: '',
    tongNguoiTrieuTap: 0,
    soNguoiCoMat: 0,
    soVang: 0,
    ghiChu: '',
    chuTichId: '',
    phoThuongTrucId: '',
    phoChuTichId: '',
    thuKyId: '',
    fileBienBan: null,
    fileKiemPhieu: null,
    tenBienBan: '',
    tenKiemPhieu: '',
    maKienToan: null,
});

// Calculate số vắng when thành viên or người tham dự changes
watch([() => hoiDong.value.tongNguoiTrieuTap, () => hoiDong.value.soNguoiCoMat], () => {
    hoiDong.value.soVang = hoiDong.value.tongNguoiTrieuTap - hoiDong.value.soNguoiCoMat;
});

const danhSachCanhan = ref([]);
// Danh sách đề xuất
const danhSachDeXuatCaNhan = ref([]);
const danhSachDeXuatDonVi = ref([]);

const madot = ref('');
const maDotDotXuat = ref('');

// Format date function
const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(date);
};

const calculateAttendanceRate = (present, total) => {
    if (!total || total === 0) return 0;
    return ((present / total) * 100).toFixed(2);
};

// Get name from ID
const getChuTriName = (id) => {
    if (!id) return 'Chưa chọn';
    const canhan = danhSachCanhan.value.find(item => item.taikhoan.PK_MaTaiKhoan === id);
    return canhan ? canhan.sTenCaNhan : 'Không xác định';
};

// Open modal to add new
const openAddModal = () => {
    resetHoiDongForm();
    isEditing.value = false;
    showDeXuatSection.value = false;
    getDeXuatForHoiDong(null)

    // Initialize new ID
    // hoiDong.value.maHoiDong = localStorage.getItem('user_name') + '-' + maDotDotXuat.value;

    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('hoiDongModal'));
    modal.show();
};

// Edit existing
const editHoiDong = (item) => {
    isEditing.value = true;
    mapHoiDongToForm(item);
    showDeXuatSection.value = true;

    // Fetch đề xuất data if needed
    getDeXuatForHoiDong(item.PK_MaHoiDong);

    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('hoiDongModal'));
    modal.show();
};

// View details
const viewHoiDong = (item) => {
    selectedHoiDong.value = item;

    // Open view modal
    const modal = new bootstrap.Modal(document.getElementById('viewHoiDongModal'));
    modal.show();
};

// Edit from view modal
const editSelectedHoiDong = () => {
    // Close view modal
    bootstrap.Modal.getInstance(document.getElementById('viewHoiDongModal')).hide();

    // Open edit modal with selected data
    editHoiDong(selectedHoiDong.value);
};

// Delete hội đồng
const deleteHoiDong = (item) => {
    Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa cuộc họp "${item.sSoHD}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#dc3545'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await axios.delete(`/api/hoidong/delete/${item.PK_MaHoiDong}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('api_token')}`
                    }
                });

                if (response.status === 200) {
                    toastSuccess('Xóa cuộc họp thành công');
                    fetchDanhSachHoiDong();
                }
            } catch (error) {
                toastError('Có lỗi xảy ra khi xóa cuộc họp');
                console.error('Lỗi xóa hội đồng:', error);
            }
        }
    });
};

// Handle file upload
const handleFileUpload = (type, event) => {
    const file = event.target.files[0];
    if (!file) return;

    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
        toastError('Kích thước file không được vượt quá 10MB!');
        event.target.value = '';
        return;
    }

    if (type === 'bienBan') {
        hoiDong.value.fileBienBan = file;
    } else if (type === 'kiemPhieu') {
        hoiDong.value.fileKiemPhieu = file;
    }
};

// Download file
const downloadFile = (fileName) => {
    if (!fileName) return;

    // Replace with actual download logic
    window.open(`/api/hoidong/download?file=${fileName}`, '_blank');
};

// Reset form
const resetHoiDongForm = () => {
    hoiDong.value = {
        maHoiDong: null,
        huongDanSo: '',
        thoiGian: '',
        diaChi: '',
        tongNguoiTrieuTap: hoiDong.value.tongNguoiTrieuTap,
        soNguoiCoMat: 0,
        soVang: 0,
        ghiChu: '',
        chuTichId: '',
        phoThuongTrucId: '',
        phoChuTichId: '',
        thuKyId: '',
        fileBienBan: null,
        fileKiemPhieu: null,
        tenBienBan: '',
        tenKiemPhieu: '',
        maKienToan: hoiDong.value.maKienToan,
    };

    danhSachDeXuatCaNhan.value = [];
    danhSachDeXuatDonVi.value = [];
};

// Map API data to form
const mapHoiDongToForm = (data) => {
    hoiDong.value = {
        maHoiDong: data.PK_MaHoiDong,
        huongDanSo: data.sSoHD || '',
        thoiGian: data.dThoiGianHop || '',
        diaChi: data.sDiaChi || '',
        tongNguoiTrieuTap: data.kien_toan.thanh_vien_hoi_dong_count || 0,
        soNguoiCoMat: data.iSoNguoiThamDu || 0,
        soVang: (data.iSoThanhVien || 0) - (data.iSoNguoiThamDu || 0),
        ghiChu: data.sGhiChu || '',
        chuTichId: data.FK_ChuTri || '',
        phoThuongTrucId: data.FK_PhoChuTichTT || '',
        phoChuTichId: data.FK_PhoChuTich || '',
        thuKyId: data.FK_ThuKy || '',
        tenBienBan: data.sTenBienBan || '',
        tenKiemPhieu: data.sTenKiemPhieu || '',
        maKienToan: data.FK_MaKienToan || null,
        fileBienBan: null,
        fileKiemPhieu: null
    };
};

// Save or update hội đồng
const saveHoiDong = async () => {
    try {
        // Validate inputs
        if (hoiDong.value.soNguoiCoMat > hoiDong.value.tongNguoiTrieuTap) {
            toastError('Số người có mặt không thể nhiều hơn tổng số người triệu tập!');
            return;
        }

        // Check required fields
        // if (!hoiDong.value.chuTichId || !hoiDong.value.thuKyId) {
        //     toastError('Vui lòng chọn Chủ tịch hội đồng và Thư ký!');
        //     return;
        // }

        // Create FormData for file upload
        const formData = new FormData();
        for (const key in hoiDong.value) {
            const value = hoiDong.value[key];

            if ((key === 'fileBienBan' || key === 'fileKiemPhieu') && value) {
                formData.append(key, value); // Chỉ append nếu có file
            } else if (key !== 'fileBienBan' && key !== 'fileKiemPhieu') {
                formData.append(key, value);
            }
        }

        let maHD = hoiDong.value.maHoiDong || (localStorage.getItem('user_name') + '-' + madot.value + '-' + hoiDong.value.thoiGian);
        let maDot = madot.value;
        let maDotXuat = maDotDotXuat.value;

        formData.append('maHoiDong', maHD);
        formData.append('maDot', maDot);
        formData.append('maDotXuat', maDotXuat);
        formData.append('dotXuat', true);

        // API call to save/update
        const url = isEditing.value ? '/api/hoidong/add' : '/api/hoidong/add';
        const response = await axios.post(url, formData, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`,
                'Content-Type': 'multipart/form-data'
            }
        });

        if (response.status === 200) {
            toastSuccess(isEditing.value
                ? 'Cập nhật thông tin hội đồng thành công!'
                : 'Tạo hội đồng mới thành công!');

            // Close modal
            // bootstrap.Modal.getInstance(document.getElementById('hoiDongModal')).hide();

            // Refresh list
            fetchDanhSachHoiDong();
        } else {
            toastError('Có lỗi xảy ra khi lưu thông tin hội đồng!');
        }
    } catch (error) {
        console.error('Lỗi lưu hội đồng:', error);
        toastError('Có lỗi xảy ra khi lưu thông tin hội đồng!');
    }
};

// Save đề xuất status
const saveDeXuat = async () => {
    try {
        // Validate data
        for (const deXuat of danhSachDeXuatCaNhan.value) {
            if (deXuat.so_nguoi_bau > hoiDong.value.soNguoiCoMat) {
                toastError(`Đề xuất cá nhân "${deXuat.danh_hieu}" có số người bầu vượt quá số người có mặt!`);
                return;
            }
        }

        for (const deXuat of danhSachDeXuatDonVi.value) {
            if (deXuat.so_nguoi_bau > hoiDong.value.soNguoiCoMat) {
                toastError(`Đề xuất đơn vị "${deXuat.danh_hieu}" có số người bầu vượt quá số người có mặt!`);
                return;
            }
        }

        // Show confirmation based on approval statistics
        let totalProposals = danhSachDeXuatCaNhan.value.length + danhSachDeXuatDonVi.value.length;
        let approvedProposals = [
            ...danhSachDeXuatCaNhan.value,
            ...danhSachDeXuatDonVi.value
        ].filter(item => item.trang_thai === '1').length;

        let confirmMessage = `Bạn sẽ duyệt ${approvedProposals}/${totalProposals} đề xuất. Xác nhận lưu?`;

        const confirmResult = await Swal.fire({
            title: 'Xác nhận xét duyệt đề xuất',
            html: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#28a745'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        // Prepare data for submission
        let formData = new FormData();

        // Add individual proposals
        danhSachDeXuatCaNhan.value.forEach(item => {
            // const proposalData = {
            //     ma_de_xuat: item.ma_de_xuat,
            //     trang_thai: item.trang_thai,
            //     so_nguoi_bau: item.so_nguoi_bau,
            //     ty_le: hoiDong.value.soNguoiCoMat > 0 ? 
            //         ((item.so_nguoi_bau / hoiDong.value.soNguoiCoMat) * 100).toFixed(2) : 0
            // };
            formData.append('deXuat[]', JSON.stringify(item));
        });

        // Add unit proposals
        danhSachDeXuatDonVi.value.forEach(item => {
            // const proposalData = {
            //     ma_de_xuat: item.PK_MaDeXuat,
            //     trang_thai: item.trang_thai,
            //     so_nguoi_bau: item.so_nguoi_bau,
            //     ty_le: hoiDong.value.soNguoiCoMat > 0 ? 
            //         ((item.so_nguoi_bau / hoiDong.value.soNguoiCoMat) * 100).toFixed(2) : 0
            // };
            formData.append('deXuat[]', JSON.stringify(item));
        });

        // Add additional required data
        formData.append('maHoiDong', hoiDong.value.maHoiDong);
        formData.append('soNguoiThamDu', hoiDong.value.soNguoiCoMat);

        // Submit data to server
        const response = await axios.post('/api/dexuat/xetduyetdexuat', formData, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200) {
            toastSuccess('Lưu danh sách đề xuất thành công!');
        
        } else {
            toastError('Có lỗi xảy ra khi lưu đề xuất');
        }
    } catch (error) {
        console.error('Lỗi xét duyệt đề xuất:', error);

        if (error.response) {
            if (error.response.status === 422) {
                const errors = error.response.data.errors || error.response.data.error;
                let errorMessage = Object.values(errors).flat().join('<br>');
                toastError(errorMessage);
            } else {
                toastError(error.response.data.message || 'Có lỗi xảy ra khi lưu đề xuất');
            }
        } else {
            toastError('Có lỗi xảy ra khi lưu danh sách đề xuất');
        }
    }
};

// Get đề xuất for a specific hội đồng
const getDeXuatForHoiDong = (maHoiDong) => {
    let url = '';
    if(maHoiDong != null) {
        url = `/api/dexuat/getlistdexuatxetduyetdotxuat/${maHoiDong}`
    }
    else {
        url = '/api/dexuat/getlistdexuatxetduyetdotxuat'
    }
    axios.get(url, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                const data = response.data.data;
                console.log(data);

                // Update proposal lists with correct data format
                danhSachDeXuatCaNhan.value = (data.de_xuat_ca_nhan || []).map(item => ({
                    ...item,
                    trang_thai: item.trang_thai?.toString() || '0',
                    so_nguoi_bau: parseInt(item.so_nguoi_bau || '0')
                }));

                danhSachDeXuatDonVi.value = (data.de_xuat_don_vi || []).map(item => ({
                    ...item,
                    trang_thai: item.trang_thai?.toString() || '0',
                    so_nguoi_bau: parseInt(item.so_nguoi_bau || '0')
                }));

                // Apply styling to selections
                setTimeout(() => applySelectStyling(), 100);
            }
        })
        .catch(error => {
            console.log(error);
            if (error.response) {
                toastError(error.response.data.message)
            }
            else {
                toastError('Có lỗi xảy ra khi tải danh sách đề xuất');
            }
        });
};

// Functions to set status for all entries at once
const setAllCaNhanStatus = (status) => {
    if (!danhSachDeXuatCaNhan.value.length) return;

    Swal.fire({
        title: status === '1'
            ? 'Duyệt tất cả đề xuất cá nhân?'
            : 'Từ chối tất cả đề xuất cá nhân?',
        text: status === '1'
            ? 'Bạn có chắc chắn muốn duyệt tất cả đề xuất cá nhân?'
            : 'Bạn có chắc chắn muốn từ chối tất cả đề xuất cá nhân?',
        icon: status === '1' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: status === '1' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: status === '1' ? 'Đồng ý duyệt' : 'Đồng ý từ chối',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            danhSachDeXuatCaNhan.value.forEach(item => {
                item.trang_thai = status;
            });

            // Update styling
            setTimeout(() => applySelectStyling(), 100);

            toastSuccess(status === '1'
                ? 'Đã đặt trạng thái Đạt cho tất cả đề xuất cá nhân'
                : 'Đã đặt trạng thái Không đạt cho tất cả đề xuất cá nhân');
        }
    });
};

const setAllDonViStatus = (status) => {
    // Similar to setAllCaNhanStatus but for đơn vị
    if (!danhSachDeXuatDonVi.value.length) return;

    Swal.fire({
        title: status === '1'
            ? 'Duyệt tất cả đề xuất đơn vị?'
            : 'Từ chối tất cả đề xuất đơn vị?',
        text: status === '1'
            ? 'Bạn có chắc chắn muốn duyệt tất cả đề xuất đơn vị?'
            : 'Bạn có chắc chắn muốn từ chối tất cả đề xuất đơn vị?',
        icon: status === '1' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonColor: status === '1' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: status === '1' ? 'Đồng ý duyệt' : 'Đồng ý từ chối',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            danhSachDeXuatDonVi.value.forEach(item => {
                item.trang_thai = status;
            });

            // Update styling
            setTimeout(() => applySelectStyling(), 100);

            toastSuccess(status === '1'
                ? 'Đã đặt trạng thái Đạt cho tất cả đề xuất đơn vị'
                : 'Đã đặt trạng thái Không đạt cho tất cả đề xuất đơn vị');
        }
    });
};

// Get all needed data
const fetchDanhSachHoiDong = () => {
    isLoading.value = true;
    axios.get('/api/hoidong/getlisthoidongdotxuat', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                danhSachHoiDong.value = response.data.data || [];
            }
        })
        .catch(error => {
            toastError('Có lỗi xảy ra khi tải danh sách hội đồng');
            console.error('Lỗi tải danh sách hội đồng:', error);
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const getListCaNhan = () => {
    axios.get('/api/taikhoan/getlistcanhan', {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('api_token')}`
        }
    })
        .then(response => {
            if (response.status === 200) {
                danhSachCanhan.value = response.data.data;
            }
        })
        .catch(error => {
            toastError('Có lỗi xảy ra khi tải danh sách cá nhân');
            console.error('Lỗi tải danh sách cá nhân:', error);
        });
};

// const getDotDotXuat = () => {
//     axios.get('/api/dotthiduakhenthuong/getdotdotxuatactive', {
//         headers: {
//             Authorization: `Bearer ${localStorage.getItem('api_token')}`
//         }
//     })
//         .then(response => {
//             if (response.status === 200) {
//                 maDotDotXuat.value = response.data.data.PK_MaDotXuat;
//             }
//         })
//         .catch(error => {
//             toastError('Có lỗi xảy ra khi tải thông tin đợt đột xuất');
//             console.error('Lỗi tải đợt đột xuất:', error);
//         });
// };

// Get kiện toàn active status
const getKienToan = async () => {
    try {
        const response = await axios.get('/api/kientoan/kientoanactive', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('api_token')}`
            }
        });

        if (response.status === 200 && response.data.data) {
            let data = response.data.data;
            hoiDong.value.maKienToan = data.PK_MaKienToan;
            hoiDong.value.tongNguoiTrieuTap = data.soThanhVien;
        }
    } catch (error) {
        console.error('Error fetching kiện toàn data:', error);
    }
};

// Apply styling to select elements
const applySelectStyling = () => {
    document.querySelectorAll('.form-select').forEach(select => {
        // Remove existing status classes
        select.classList.remove('select-1', 'select-khong-1');

        // Add appropriate class based on selected value
        if (select.value === '1') {
            select.classList.add('select-1');
        } else if (select.value === '0') {
            select.classList.add('select-khong-1');
        }
    });
};

// Watch for changes and apply styling
watch(danhSachDeXuatCaNhan, () => {
    setTimeout(() => applySelectStyling(), 100);
}, { deep: true });

watch(danhSachDeXuatDonVi, () => {
    setTimeout(() => applySelectStyling(), 100);
}, { deep: true });

// Initialize
onMounted(async () => {
    try {
        madot.value = useGlobalStore().dotActive;
        // getDotDotXuat();
        getListCaNhan();
        fetchDanhSachHoiDong();
        getKienToan();

        // Add event listener for select styling
        document.addEventListener('change', (event) => {
            if (event.target.classList.contains('form-select')) {
                applySelectStyling();
            }
        });
    } catch (error) {
        console.error('Lỗi khởi tạo:', error);
    }
});
</script>

<style scoped>
.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

/* Custom styling for status selects */
.form-select {
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select option[value="1"] {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
    font-weight: 500;
}

.form-select option[value="0"] {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    font-weight: 500;
}

/* Apply custom styling to selects based on their selected value */
.form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select-1 {
    border-color: #28a745 !important;
    color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2328a745' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}

.select-khong-1 {
    border-color: #dc3545 !important;
    color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23dc3545' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
}
</style>