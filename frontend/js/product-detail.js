
/* ===== DỮ LIỆU CHI TIẾT SẢN PHẨM ===== */
const products = {
    1: {
        name: "Mô hình Gundam HG 1/144 RX-0 Unicorn Gundam 03 Phenex (Destroy Mode) (Narrative Ver.) - Bandai",
        price: 250000,
        image: "assets/images/gundam-hg-001.jpg",
        code: "HG-001",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 001 dành cho người yêu thích lắp ráp và sưu tầm."
    },
    2: {
        name: "Mô hình Gundam HG 1/144 ORX-139 Hambrabi (GQ) - Bandai",
        price: 280000,
        image: "assets/images/gundam-hg-002.png",
        code: "HG-002",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 002 phù hợp cho người yêu thích lắp ráp mô hình."
    },
    3: {
        name: "Mô hình Gundam HG 1/144 VGMM-Git01 Kabakali - Bandai",
        price: 300000,
        image: "assets/images/gundam-hg-003.png",
        code: "HG-003",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 003 dành cho bộ sưu tập Gundam của bạn."
    },
    4: {
        name: "Mô hình Gundam HG 1/144 NMX-004 Qubeley Papillon - Bandai",
        price: 320000,
        image: "assets/images/gundam-hg-004.png",
        code: "HG-004",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 004 dành cho người đam mê mô hình robot."
    },
    5: {
        name: "Mô hình Gundam HG 1/144 GX-9999 Gundam X Maoh - Bandai",
        price: 350000,
        image: "assets/images/gundam-hg-005.png",
        code: "HG-005",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 005 phù hợp để lắp ráp và trưng bày."
    },
    6: {
        name: "Mô hình Gundam HG 1/144 MS-06R-AB Zaku Amazing - Bandai",
        price: 380000,
        image: "assets/images/gundam-hg-006.png",
        code: "HG-006",
        type: "High Grade (HG)",
        description: "Mô hình Gundam HG 006 dành cho người yêu thích sưu tầm."
    }
};

/* ===== ĐỌC MÃ SẢN PHẨM TỪ ĐƯỜNG DẪN ===== */
const params = new URLSearchParams(window.location.search);
const productId = params.get("id") || "1";
const product = products[productId] || products[1];

/* ===== CẬP NHẬT THÔNG TIN TRÊN TRANG ===== */
document.getElementById("detail-image").src = product.image;
document.getElementById("detail-image").alt = product.name;
document.getElementById("detail-name").textContent = product.name;
document.getElementById("detail-price").textContent =
    product.price.toLocaleString("vi-VN") + " VNĐ";
document.getElementById("detail-code").textContent = product.code;
document.getElementById("detail-type").textContent = product.type;
document.getElementById("detail-description").textContent = product.description;

const detailAddToCart = document.getElementById("detail-add-to-cart");

if (detailAddToCart) {
    detailAddToCart.addEventListener("click", function () {
        let cart = JSON.parse(localStorage.getItem("gunplahub-cart")) || [];

        const existingProduct = cart.find(function (item) {
            return item.name === product.name;
        });

        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push({
                name: product.name,
                price: product.price,
                quantity: 1
            });
        }

        localStorage.setItem("gunplahub-cart", JSON.stringify(cart));

        alert(product.name + " đã được thêm vào giỏ hàng!");
    });
}