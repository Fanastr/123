window.addEventListener("DOMContentLoaded", () => {
    const conainerOrderCancel = document.querySelector("#confirm");
    const btClose = conainerOrderCancel.querySelector("#close-confirm");
    const btSubmit = conainerOrderCancel.querySelector("form input[type='submit']");
    const pName = conainerOrderCancel.querySelector("form .name");
    const pPrice = conainerOrderCancel.querySelector("form .price");
    const inputIDOrder = conainerOrderCancel.querySelector("form #id-basket");

    btClose.onclick = () => {
        btSubmit.disabled = true;
        document.body.style.overflow = null;
        conainerOrderCancel.classList.remove("confirm--show");
    };

    conainerOrderCancel.onmousedown = function(e) {
        if (e.target == this && e.button === 0) {
            btClose.onclick();
        };
    };

    document.querySelectorAll(".basket-cancel").forEach(btCancel => {
        const name = btCancel.parentElement.querySelector(".name").textContent;
        const price = btCancel.parentElement.querySelector(".price").textContent;
        
        btCancel.onclick = () => {
            inputIDOrder.value = btCancel.id;
            pName.textContent = `Товар: ${name}`;
            pPrice.textContent = `Цена: ${price}`;

            document.body.style.overflow = "hidden";
            conainerOrderCancel.classList.add("confirm--show");
            btSubmit.disabled = false;
        };
    });
});