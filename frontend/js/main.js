
let cart = JSON.parse(localStorage.getItem("gunplahub-cart")) || [];

const cartCount = document.getElementById("cart-count");

function updateCartCount() {
    const currentCart = JSON.parse(
        localStorage.getItem("gunplahub-cart")
    ) || [];

    const countElement = document.getElementById("cart-count");

    if (countElement) {
        countElement.textContent = currentCart.reduce(function (sum, product) {
            return sum + product.quantity;
        }, 0);
    }
}

updateCartCount();

window.addEventListener("pageshow", function () {
    updateCartCount();
});

window.addEventListener("storage", function (event) {
    if (event.key === "gunplahub-cart") {
        updateCartCount();
    }
});



const cartButtons = document.querySelectorAll(".add-to-cart");
console.log("Tổng số nút:", cartButtons.length);
console.log(
    "Số nút nằm trong sản phẩm:",
    document.querySelectorAll(".product-card .add-to-cart").length
);

cartButtons.forEach(function (button) {
    button.addEventListener("click", function () {

        // Đọc lại giỏ hàng mới nhất trước khi thêm sản phẩm
        cart = JSON.parse(localStorage.getItem("gunplahub-cart")) || [];
        
        const productCard = button.closest(".product-card");
        const productName = productCard.querySelector(".card-title").textContent;

        
const productPriceText = productCard.querySelector(".product-price").textContent;
const productPrice = Number(
    productPriceText.replace(/[^\d]/g, "")
);


const existingProduct = cart.find(function (product) {
    return product.name === productName;
});

if (existingProduct) {
    existingProduct.quantity += 1;
} else {
    cart.push({
        name: productName,
        price: productPrice,
        quantity: 1
    });
}

localStorage.setItem("gunplahub-cart", JSON.stringify(cart));

if (cartCount) {
    cartCount.textContent = cart.reduce(function (sum, product) {
        return sum + product.quantity;
    }, 0);
}

renderCart();

alert("Đã thêm " + productName + " vào giỏ hàng!");
console.log("Giỏ hàng hiện tại:", cart);
    });
});



function renderCart() {
    const cartItems = document.getElementById("cart-items");
    const cartTotal = document.getElementById("cart-total");

    // Nếu trang hiện tại không có khu vực giỏ hàng thì dừng hàm
    if (!cartItems || !cartTotal) {
        return;
    }

    cartItems.innerHTML = "";

    if (cart.length === 0) {
        cartItems.innerHTML =
            '<p class="text-secondary">Chưa có sản phẩm nào trong giỏ hàng.</p>';
        cartTotal.textContent = "0 VNĐ";
        return;
    }

    let total = 0;

    
cart.forEach(function (product) {
    total += product.price * product.quantity;

    const item = document.createElement("div");
    item.className = "border rounded p-3 mb-2";

    const name = document.createElement("p");
    name.className = "mb-1 fw-bold";
    name.textContent = product.name + " × " + product.quantity;

    const price = document.createElement("p");
    price.className = "mb-2 text-danger";
    price.textContent =
        (product.price * product.quantity).toLocaleString("vi-VN") +
        " VNĐ";

    const deleteButton = document.createElement("button");
    deleteButton.type = "button";
    deleteButton.className = "btn btn-sm btn-danger";
    deleteButton.textContent = "Xóa";

    deleteButton.addEventListener("click", function () {
        if (product.quantity > 1) {
            product.quantity -= 1;
        } else {
            cart = cart.filter(function (item) {
                return item.name !== product.name;
            });
        }
    
        localStorage.setItem("gunplahub-cart", JSON.stringify(cart));
    
        renderCart();
    
        if (cartCount) {
            cartCount.textContent = cart.reduce(function (sum, item) {
                return sum + item.quantity;
            }, 0);
        }
    });

    item.appendChild(name);
    item.appendChild(price);
    item.appendChild(deleteButton);
    cartItems.appendChild(item);
});

    cartTotal.textContent = total.toLocaleString("vi-VN") + " VNĐ";
}
// Hiển thị giỏ hàng đã lưu khi mở website
renderCart();


if (cartCount) {
    cartCount.textContent = cart.reduce(function (sum, product) {
        return sum + product.quantity;
    }, 0);
}

/* ===== CHỨC NĂNG TÌM KIẾM SẢN PHẨM ===== */

const searchForm = document.getElementById("search-form");
const searchInput = document.getElementById("search-product");

if (searchForm && searchInput) {
    const productCards = document.querySelectorAll(
        "#san-pham .product-card"
    );

    function searchProducts() {
        const keyword = searchInput.value.toLowerCase().trim();

        productCards.forEach(function (card) {
            const productName = card
                .querySelector(".card-title")
                .textContent.toLowerCase();

            const productCode = productName.match(/hg\s*\d+/)?.[0] || "";

            const isMatch =
                productName.includes(keyword) ||
                productCode.includes(keyword);

            card.parentElement.style.display = isMatch ? "" : "none";
        });
    }

    searchInput.addEventListener("input", searchProducts);

    searchForm.addEventListener("submit", function (event) {
        event.preventDefault();
        searchProducts();

        document.getElementById("san-pham").scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    });
}
renderCart();