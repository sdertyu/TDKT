export default function authMiddleware(to, from, next) {
    const apiToken = sessionStorage.getItem("api_token");

    if (!apiToken) {
        return next("/login"); // Nếu không có token, chuyển hướng login
    }

    // Lấy role từ sessionStorage và ép kiểu thành số
    const userRole = Number(sessionStorage.getItem("role")) || 0; // Mặc định role = 0 nếu không có

    // Kiểm tra quyền truy cập nếu route có yêu cầu quyền cụ thể
    if (to.meta.roles && !to.meta.roles.includes(userRole)) {
        return next("/403"); // Không có quyền → Chuyển hướng 403
    }

    next(); // Nếu hợp lệ, cho phép truy cập
}
