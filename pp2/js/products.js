function openModal(title, text, img) {
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-text').innerText = text;
    document.getElementById('modal-img').src = img;
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

function addToCart(id) {
    alert("Товар " + id + " добавлен в корзину");
}