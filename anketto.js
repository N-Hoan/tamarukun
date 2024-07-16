document.addEventListener('DOMContentLoaded', function() {
    const menuIcon = document.getElementById('hamburgerMenu');
    const menuList = document.getElementById('menuList');

    menuIcon.addEventListener('click', function() {
        menuIcon.classList.toggle('menuOpen');
        menuList.classList .toggle('menuOpen');
    });
});
function calcRate(r) {
    const f = ~~r,//Tương tự Math.floor(r)
    id = 'star' + f + (r % f ? 'half' : '')
    id && (document.getElementById(id).checked = !0)
}

document.getElementById('registrationForm').addEventListener('submit', function (event) {
    event.preventDefault();
    window.location.href = './feedback.php'; // Thay đổi thành trang bạn muốn chuyển hướng tới
    window.location.href = './feebback_test.php';
});
    