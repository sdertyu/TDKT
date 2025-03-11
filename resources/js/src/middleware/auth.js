export default function authMiddleware(to, from, next) {
    const apiToken = localStorage.getItem('api_token');
    if (!apiToken) {
        // Nếu không có token thì chuyển về trang đăng nhập
        return next('/login');
    }
    next(); // Nếu có token thì cho phép truy cập trang đó
}
