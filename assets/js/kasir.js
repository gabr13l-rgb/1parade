let total = 0;
let orders = [];

function tambahPesanan(id, nama, harga) {
  const existing = orders.find(item => item.id === id);
  if (existing) {
    existing.qty++;
  } else {
    orders.push({ id, nama, harga, qty: 1 });
  }
  renderOrder();
}

function hapusPesanan(id) {
  orders = orders.filter(item => item.id !== id);
  renderOrder();
}

function renderOrder() {
  const orderList = document.getElementById("orderList");
  orderList.innerHTML = "";
  total = 0;
  orders.forEach(item => {
    const subtotal = item.harga * item.qty;
    total += subtotal;
    orderList.innerHTML += `
      <div class="order-item">
        <span>${item.nama} x${item.qty}</span>
        <span>Rp ${subtotal.toLocaleString()}</span>
        <button onclick="hapusPesanan(${item.id})"><i class='fas fa-times'></i></button>
      </div>`;
  });
  document.getElementById("total").innerText = total.toLocaleString();
  hitungKembalian();
}

function hitungKembalian() {
  const uang = parseInt(document.getElementById("uang").value) || 0;
  const kembali = uang - total;
  const tampil = document.getElementById("kembalian");
  tampil.innerText = kembali >= 0 ? "Kembalian: Rp " + kembali.toLocaleString() : "";
}

function bayar() {
  const metode = document.getElementById("metode_pembayaran").value;
  const uang = parseInt(document.getElementById("uang").value) || 0;
  const kembali = uang - total;

  if (orders.length === 0) {
    alert("Belum ada menu yang dipesan.");
    return;
  }

  if (metode === "Tunai" && uang < total) {
    alert("Uang tidak cukup untuk pembayaran.");
    return;
  }

  const items = orders.map(item => ({
    menu_id: item.id,
    jumlah: item.qty,
    subtotal: item.harga * item.qty
  }));

  fetch("proses.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      total: total,
      bayar: uang,
      kembalian: kembali,
      metode: metode,
      items: items
    })
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      alert("Transaksi berhasil! Total: Rp " + total.toLocaleString());
      orders = [];
      total = 0;
      document.getElementById("uang").value = "";
      document.getElementById("kembalian").innerText = "";
      renderOrder();
    } else {
      alert("Gagal menyimpan transaksi: " + res.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert("Terjadi kesalahan saat menyimpan transaksi.");
  });
}

document.getElementById("searchMenu").addEventListener("keyup", function () {
  const keyword = this.value.toLowerCase();
  const cards = document.querySelectorAll(".menu-card");
  cards.forEach(card => {
    const title = card.querySelector("h4").textContent.toLowerCase();
    card.style.display = title.includes(keyword) ? "block" : "none";
  });
});

document.getElementById("metode_pembayaran").addEventListener("change", function () {
  const metode = this.value;
  const totalText = document.getElementById("total").textContent.replace(/\./g, '');
  const total = Number(totalText) || 0;

  if (metode === "QRIS") {
    document.getElementById("uang").value = total;
    hitungKembalian();
  } else {
    document.getElementById("uang").value = "";
    hitungKembalian();
  }
});

window.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".grid");
  const cards = Array.from(grid.querySelectorAll(".menu-card"));

  cards.sort((a, b) => {
    const nameA = a.querySelector("h4").textContent.toLowerCase();
    const nameB = b.querySelector("h4").textContent.toLowerCase();
    return nameA.localeCompare(nameB);
  });

  cards.forEach(card => grid.appendChild(card));
});
