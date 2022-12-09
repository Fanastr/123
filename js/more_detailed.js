window.addEventListener("DOMContentLoaded", () => {
    const btBuy = document.querySelector("#buy");

    if (btBuy) {
        const confirmPurchase = document.querySelector("#confirm-purchase");
        const btSubmit = confirmPurchase.querySelector("form input[type='submit']");
        const btClose = confirmPurchase.querySelector("#close-confirm-purchase");

        btClose.onclick = () => {
            btSubmit.disabled = true;
            document.body.style.overflow = null;
            confirmPurchase.classList.remove("confirm-purchase--show");
        };

        confirmPurchase.onmousedown = function(e) {
            if (e.target == this && e.button === 0) {
                btClose.onclick();
            };
        };

        btBuy.onclick = () => {
            document.body.style.overflow = "hidden";
            confirmPurchase.classList.add("confirm-purchase--show");
            btSubmit.disabled = false;
        };
    };
});