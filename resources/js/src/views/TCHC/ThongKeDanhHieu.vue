<template>
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Thống kê danh hiệu</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
              <li class="breadcrumb-item active">Thống kê danh hiệu</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Bộ lọc thống kê</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Danh hiệu</label>
                      <select v-model="selectedDanhHieu" class="form-control">
                        <option value="">Tất cả danh hiệu</option>
                        <option v-for="danhHieu in danhHieuList" :key="danhHieu.id" :value="danhHieu.id">
                          {{ danhHieu.tenDanhHieu }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Đợt thi đua</label>
                      <select v-model="selectedDot" class="form-control">
                        <option value="">Tất cả đợt</option>
                        <option v-for="dot in dotList" :key="dot.id" :value="dot.id">
                          {{ dot.tenDot }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Năm</label>
                      <select v-model="selectedNam" class="form-control">
                        <option value="">Tất cả các năm</option>
                        <option v-for="nam in namList" :key="nam" :value="nam">
                          {{ nam }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Đơn vị</label>
                      <select v-model="selectedDonVi" class="form-control">
                        <option value="">Tất cả đơn vị</option>
                        <option v-for="donVi in donViList" :key="donVi.id" :value="donVi.id">
                          {{ donVi.tenDonVi }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12">
                    <button @click="applyFilter" class="btn btn-primary">
                      <i class="fas fa-filter mr-1"></i> Lọc dữ liệu
                    </button>
                    <button @click="resetFilter" class="btn btn-secondary ml-2">
                      <i class="fas fa-sync-alt mr-1"></i> Đặt lại
                    </button>
                    <button @click="exportData" class="btn btn-success float-right">
                      <i class="fas fa-file-excel mr-1"></i> Xuất Excel
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Kết quả bầu chọn danh hiệu</h3>
                <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" v-model="searchQuery" class="form-control float-right" placeholder="Tìm kiếm">
                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>STT</th>
                      <th>Họ và tên</th>
                      <th>Đơn vị</th>
                      <th>Danh hiệu</th>
                      <th>Đợt thi đua</th>
                      <th>Năm</th>
                      <th>Số phiếu bầu</th>
                      <th>Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredResults.length === 0">
                      <td colspan="8" class="text-center">Không có dữ liệu</td>
                    </tr>
                    <tr v-for="(item, index) in filteredResults" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.hoTen }}</td>
                      <td>{{ item.donVi }}</td>
                      <td>{{ item.danhHieu }}</td>
                      <td>{{ item.dotThiDua }}</td>
                      <td>{{ item.nam }}</td>
                      <td>{{ item.soPhieuBau }}</td>
                      <td>
                        <span :class="getStatusClass(item.trangThai)">
                          {{ item.trangThai }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">«</a>
                  </li>
                  <li v-for="page in totalPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">»</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thống kê theo danh hiệu</h3>
              </div>
              <div class="card-body">
                <canvas id="danhHieuChart"></canvas>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thống kê theo đơn vị</h3>
              </div>
              <div class="card-body">
                <canvas id="donViChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

import Chart from 'chart.js/auto';

// State
const danhHieuList = ref([]);
const dotList = ref([]);
const namList = ref([]);
const donViList = ref([]);
const ketQuaList = ref([]);

const selectedDanhHieu = ref('');
const selectedDot = ref('');
const selectedNam = ref('');
const selectedDonVi = ref('');
const searchQuery = ref('');

const currentPage = ref(1);
const itemsPerPage = ref(10);

// Fetch data
const fetchDanhHieu = async () => {
  try {
    const response = await axios.get('/api/danhhieu');
    danhHieuList.value = response.data;
  } catch (error) {
    console.error('Lỗi khi lấy danh sách danh hiệu:', error);
  }
};

const fetchDotThiDua = async () => {
  try {
    const response = await axios.get('/api/dottdkt');
    dotList.value = response.data;
  } catch (error) {
    console.error('Lỗi khi lấy danh sách đợt thi đua:', error);
  }
};

const fetchDonVi = async () => {
  try {
    const response = await axios.get('/api/donvi');
    donViList.value = response.data;
  } catch (error) {
    console.error('Lỗi khi lấy danh sách đơn vị:', error);
  }
};

const fetchKetQua = async () => {
  try {
    const response = await axios.get('/api/ketquabaudon');
    ketQuaList.value = response.data;
    
    // Tạo danh sách năm từ dữ liệu
    const years = [...new Set(ketQuaList.value.map(item => item.nam))];
    namList.value = years.sort((a, b) => b - a);
    
    renderCharts();
  } catch (error) {
    console.error('Lỗi khi lấy kết quả bầu chọn:', error);
  }
};

// Computed properties
const filteredResults = computed(() => {
  let results = [...ketQuaList.value];
  
  if (selectedDanhHieu.value) {
    results = results.filter(item => item.danhHieuId === selectedDanhHieu.value);
  }
  
  if (selectedDot.value) {
    results = results.filter(item => item.dotThiDuaId === selectedDot.value);
  }
  
  if (selectedNam.value) {
    results = results.filter(item => item.nam === selectedNam.value);
  }
  
  if (selectedDonVi.value) {
    results = results.filter(item => item.donViId === selectedDonVi.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(item => 
      item.hoTen.toLowerCase().includes(query) || 
      item.donVi.toLowerCase().includes(query) ||
      item.danhHieu.toLowerCase().includes(query)
    );
  }
  
  // Phân trang
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  return results.slice(startIndex, startIndex + itemsPerPage.value);
});

const totalItems = computed(() => {
  let results = [...ketQuaList.value];
  
  if (selectedDanhHieu.value) {
    results = results.filter(item => item.danhHieuId === selectedDanhHieu.value);
  }
  
  if (selectedDot.value) {
    results = results.filter(item => item.dotThiDuaId === selectedDot.value);
  }
  
  if (selectedNam.value) {
    results = results.filter(item => item.nam === selectedNam.value);
  }
  
  if (selectedDonVi.value) {
    results = results.filter(item => item.donViId === selectedDonVi.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    results = results.filter(item => 
      item.hoTen.toLowerCase().includes(query) || 
      item.donVi.toLowerCase().includes(query) ||
      item.danhHieu.toLowerCase().includes(query)
    );
  }
  
  return results.length;
});

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / itemsPerPage.value);
});

// Methods
const applyFilter = () => {
  currentPage.value = 1;
  renderCharts();
};

const resetFilter = () => {
  selectedDanhHieu.value = '';
  selectedDot.value = '';
  selectedNam.value = '';
  selectedDonVi.value = '';
  searchQuery.value = '';
  currentPage.value = 1;
  renderCharts();
};

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const getStatusClass = (status) => {
  switch (status) {
    case 'Đã duyệt':
      return 'badge badge-success';
    case 'Chờ duyệt':
      return 'badge badge-warning';
    case 'Từ chối':
      return 'badge badge-danger';
    default:
      return 'badge badge-info';
  }
};

const exportData = () => {
  // Xử lý xuất dữ liệu ra Excel
  alert('Chức năng xuất Excel đang được phát triển');
};

let danhHieuChart = null;
let donViChart = null;

const renderCharts = () => {
  // Thống kê theo danh hiệu
  const danhHieuData = {};
  ketQuaList.value.forEach(item => {
    if (!danhHieuData[item.danhHieu]) {
      danhHieuData[item.danhHieu] = 0;
    }
    danhHieuData[item.danhHieu]++;
  });
  
  // Thống kê theo đơn vị
  const donViData = {};
  ketQuaList.value.forEach(item => {
    if (!donViData[item.donVi]) {
      donViData[item.donVi] = 0;
    }
    donViData[item.donVi]++;
  });
  
  // Render biểu đồ danh hiệu
  if (danhHieuChart) {
    danhHieuChart.destroy();
  }
  
  const danhHieuCtx = document.getElementById('danhHieuChart');
  if (danhHieuCtx) {
    danhHieuChart = new Chart(danhHieuCtx, {
      type: 'pie',
      data: {
        labels: Object.keys(danhHieuData),
        datasets: [{
          data: Object.values(danhHieuData),
          backgroundColor: [
            '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de',
            '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d', '#343a40'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
          },
          title: {
            display: true,
            text: 'Phân bố theo danh hiệu'
          }
        }
      }
    });
  }
  
  // Render biểu đồ đơn vị
  if (donViChart) {
    donViChart.destroy();
  }
  
  const donViCtx = document.getElementById('donViChart');
  if (donViCtx) {
    donViChart = new Chart(donViCtx, {
      type: 'bar',
      data: {
        labels: Object.keys(donViData),
        datasets: [{
          label: 'Số lượng danh hiệu',
          data: Object.values(donViData),
          backgroundColor: '#3c8dbc'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'Phân bố theo đơn vị'
          }
        }
      }
    });
  }
};

// Lifecycle hooks
onMounted(() => {
  fetchDanhHieu();
  fetchDotThiDua();
  fetchDonVi();
  fetchKetQua();
});
</script>