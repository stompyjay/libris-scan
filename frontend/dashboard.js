const API_BASE_URL = '/api'; 

let currentBooks = [];
let cart = [];
let isAdmin = false; 

// --- ANIMACIÓN AL HACER SCROLL ---
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.classList.add('visible');
            }, index * 50);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

function animarTarjetas() {
    const cards = document.querySelectorAll('.book-card');
    cards.forEach(card => observer.observe(card));
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
    return null;
}

// --- NUEVA LÓGICA DE CARGA ---
function toggleLoader(show) { 
    const overlay = document.getElementById('loading-overlay');
    if(show) overlay.style.display = 'flex'; 
    else overlay.style.display = 'none';
}

// --- TOGGLE CARRITO MÓVIL (NUEVO) ---
function toggleCart() {
    const cartElement = document.getElementById('drop-zone');
    // En móvil usamos 'translate-x-full' para ocultarlo. Si lo quitamos, se ve.
    // toggle('translate-x-full') -> Si está, lo quita (se muestra). Si no está, lo pone (se oculta).
    cartElement.classList.toggle('translate-x-full');
}

// --- BÚSQUEDAS ---
async function searchBooks() {
    const query = document.getElementById('api-search').value;
    if(!query) return;
    
    toggleLoader(true); 
    document.getElementById('api-results-grid').innerHTML = ''; 

    try {
        const res = await fetch(`https://openlibrary.org/search.json?q=${query}&limit=12`);
        const data = await res.json();
        await new Promise(r => setTimeout(r, 2000)); 
        renderBooks(data.docs, 'api');
    } catch (e) { console.error(e); } finally { toggleLoader(false); }
}

async function searchByCategory(isInitial = false) {
    const cat = document.getElementById('category-select').value;
    if(!cat) return;
    
    if (!isInitial) {
        toggleLoader(true);
        document.getElementById('api-results-grid').innerHTML = '';
    }

    try {
        const res = await fetch(`https://openlibrary.org/subjects/${cat}.json?limit=12`);
        const data = await res.json();
        if (!isInitial) await new Promise(r => setTimeout(r, 2000));
        renderBooks(data.works, 'api');
    } catch (e) { console.error(e); } finally { if (!isInitial) toggleLoader(false); }
}

// --- PERFIL Y ADMIN ---
async function loadUserProfile() {
    try {
        const response = await fetch(`${API_BASE_URL}/user`, {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (response.status === 401) { window.location.href = '/'; return; }

        if (response.ok) {
            const userData = await response.json();
            document.getElementById('sidebar-name').textContent = userData.name;
            document.getElementById('header-name').textContent = userData.name;
            
            if (userData.admin === 1 || userData.role === 'admin' || userData.is_admin === 1) {
                isAdmin = true;
                document.getElementById('admin-toolbar').classList.remove('hidden');
            }
        }
    } catch (error) { console.error("Error cargando perfil:", error); }
}

async function loadCategoriesForAdmin() {
    try {
        const res = await fetch('/api/categories'); 
        if (!res.ok) return;
        const categories = await res.json();
        const select = document.getElementById('edit-category');
        select.innerHTML = ''; 
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.text = cat.name;
            select.appendChild(option);
        });
    } catch (e) { console.error(e); }
}

// --- RENDERIZADO ---
function renderBooks(books, source = 'api') {
    currentBooks = books; 
    const grid = document.getElementById('api-results-grid');
    const titleHeader = document.getElementById('results-title');

    if (source === 'local') titleHeader.innerHTML = '<i class="fas fa-database text-blue-500" aria-hidden="true"></i> Mi Inventario (Base de Datos)';
    else titleHeader.innerHTML = '<i class="fas fa-sparkles text-yellow-500" aria-hidden="true"></i> Libros Destacados';
    
    if (!books || books.length === 0) {
        grid.innerHTML = '<p class="text-gray-500 col-span-full text-center py-10">No hay libros para mostrar.</p>';
        return;
    }

    const html = books.map((book, index) => {
        const isLocal = source === 'local';
        const cover = isLocal ? (book.cover || 'https://placehold.co/150x220?text=Sin+Img') : getBestCover(book);
        const author = isLocal ? (book.author || 'Desconocido') : getBestAuthor(book);
        
        let categoryName = 'General';
        if (isLocal) {
            if (book.category && book.category.name) categoryName = book.category.name;
        } else {
            categoryName = getBestCategory(book);
        }

        const price = isLocal ? book.price + '€' : '9.95€';
        const titleSafe = (book.title || "Sin Título").replace(/"/g, '&quot;'); 

        let adminBtns = '';
        if (isLocal && isAdmin) {
            adminBtns = `
            <div class="absolute top-2 left-2 z-20 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition duration-300">
                <button onclick="event.stopPropagation(); openAdminModal(${index})" class="bg-yellow-500 text-black w-8 h-8 rounded-full flex items-center justify-center hover:scale-110 shadow-lg" title="Editar" aria-label="Editar ${titleSafe}">
                    <i class="fas fa-pencil-alt text-xs" aria-hidden="true"></i>
                </button>
                <button onclick="event.stopPropagation(); deleteBook(${book.id})" class="bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center hover:scale-110 shadow-lg" title="Borrar" aria-label="Borrar ${titleSafe}">
                    <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                </button>
            </div>`;
        }

        return `
            <article class="book-card cursor-pointer group relative bg-[#1a1a1e] flex flex-col h-full focus:ring-2 focus:ring-blue-500 outline-none"
                tabindex="0"
                role="button"
                aria-label="Ver detalles de ${titleSafe}"
                onclick="openProductModal(${index})" 
                onkeydown="if(event.key === 'Enter') openProductModal(${index})"
                draggable="true"
                ondragstart="empezarArrastre(event, ${index})">
                
                ${adminBtns}

                <div class="overflow-hidden h-56 relative bg-gray-800 w-full">
                    <img src="${cover}" alt="Portada de ${titleSafe}" loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500 pointer-events-none">
                    <span class="absolute top-2 right-2 bg-black/60 backdrop-blur-md text-white text-[10px] px-2 py-1 rounded-lg border border-white/10 shadow-lg">
                        ${categoryName}
                    </span>
                </div>

                <div class="p-4 flex flex-col flex-1 w-full">
                    <h3 class="font-bold text-xs truncate text-white text-left mb-1">${book.title}</h3>
                    <p class="text-[10px] text-gray-300 truncate text-left mb-2">${author}</p>
                    <div class="mt-auto pt-2 border-t border-gray-800 flex justify-between items-center">
                        <span class="text-green-400 font-bold text-sm">${price}</span>
                        <i class="fas fa-cart-plus text-gray-500 text-xs" aria-hidden="true"></i>
                    </div>
                </div>
            </article>
        `;
    }).join('');

    grid.innerHTML = html;
    animarTarjetas();
}

// --- CARRITO ---
function empezarArrastre(event, index) {
    event.dataTransfer.setData('text/plain', index);
    event.dataTransfer.effectAllowed = "copy";
}

// Dropzone solo existe en Desktop, en móvil es click para añadir desde modal
const dropZone = document.getElementById('drop-zone');
if(dropZone) {
    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-blue-500', 'bg-blue-900/20'); });
    dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-blue-500', 'bg-blue-900/20'); });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-blue-500', 'bg-blue-900/20');
        const index = e.dataTransfer.getData('text/plain');
        if(index !== "") {
            const libroOriginal = currentBooks[index];
            if (libroOriginal) prepararYAgregar(libroOriginal);
        }
    });
}

function prepararYAgregar(rawBook) {
    const isLocal = rawBook.id && rawBook.created_at; 
    let catName = isLocal ? (rawBook.category?.name || 'General') : getBestCategory(rawBook);

    const bookForCart = {
        title: rawBook.title,
        author: isLocal ? rawBook.author : getBestAuthor(rawBook),
        cover: isLocal ? rawBook.cover : getBestCover(rawBook),
        price: isLocal ? rawBook.price : 9.95,
        suggested_category: catName
    };
    addToCart(bookForCart);
}

function addToCart(book) {
    if(cart.find(b => b.title === book.title)) return;
    cart.push(book);
    renderCart();
    
    // Si estamos en móvil, mostrar el carrito brevemente o notificar
    // Para esta demo, simplemente actualizamos el contador
}

function renderCart() {
    const cartList = document.getElementById('cart-list');
    const emptyMsg = document.getElementById('empty-msg');
    const emptyMsgMobile = document.getElementById('empty-msg-mobile');
    const cartActions = document.getElementById('cart-actions');
    const mobileBadge = document.getElementById('mobile-cart-badge');
    
    // Actualizar badge móvil
    if(cart.length > 0) {
        mobileBadge.classList.remove('hidden');
        mobileBadge.classList.add('flex');
        mobileBadge.innerText = cart.length;
    } else {
        mobileBadge.classList.add('hidden');
        mobileBadge.classList.remove('flex');
    }

    if (cart.length > 0) {
        if(emptyMsg) emptyMsg.style.display = 'none';
        if(emptyMsgMobile) emptyMsgMobile.style.display = 'none';
        cartList.classList.remove('hidden');
        cartActions.classList.remove('hidden');
        dropZone.classList.remove('border-dashed');
    } else {
        if(emptyMsg) emptyMsg.style.display = 'block';
        if(emptyMsgMobile) emptyMsgMobile.style.display = 'flex';
        cartList.classList.add('hidden');
        cartActions.classList.add('hidden');
        dropZone.classList.add('border-dashed');
    }

    cartList.innerHTML = cart.map((book, index) => `
        <div class="flex items-center gap-2 bg-black/40 p-2 rounded border border-gray-700 animate-[fadeIn_0.3s]">
            <img src="${book.cover}" alt="Portada de ${book.title}" class="w-8 h-12 object-cover rounded">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold truncate text-white">${book.title}</p>
                <p class="text-[9px] text-gray-400">${book.price}€</p>
            </div>
            <button onclick="removeFromCart(${index})" class="text-red-400 hover:text-white px-2" aria-label="Eliminar ${book.title} del carrito">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    `).join('');

    const total = cart.reduce((sum, item) => sum + Number(item.price), 0);
    document.getElementById('cart-total').innerText = total.toFixed(2) + '€';
}

window.removeFromCart = function(index) {
    cart.splice(index, 1);
    renderCart();
};

window.checkoutAll = function() {
    if(cart.length === 0) { alert("El carrito está vacío"); return; }
    localStorage.setItem('carritoParaPagar', JSON.stringify(cart));
    window.location.href = "/checkout"; 
};

// --- MODALES Y HELPERS ---
function openProductModal(index) {
    const book = currentBooks[index];
    const isLocal = book.created_at ? true : false;
    const cover = isLocal ? book.cover : getBestCover(book);
    const author = isLocal ? book.author : getBestAuthor(book);

    const imgEl = document.getElementById('modal-img');
    imgEl.src = cover;
    imgEl.alt = `Portada completa de ${book.title}`; 
    
    document.getElementById('modal-title').innerText = book.title;
    document.getElementById('modal-author').innerText = author;
    
    const btn = document.getElementById('buy-btn');
    btn.onclick = () => {
        prepararYAgregar(book);
        closeModal('product-modal');
        // Opcional: abrir el carrito automáticamente en móvil
        if(window.innerWidth < 768) {
             document.getElementById('drop-zone').classList.remove('translate-x-full');
        }
    };
    
    document.getElementById('product-modal').classList.remove('hidden');
}

function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function getBestCover(book) {
    if (book.cover_id) return `https://covers.openlibrary.org/b/id/${book.cover_id}-M.jpg`;
    if (book.cover_i) return `https://covers.openlibrary.org/b/id/${book.cover_i}-M.jpg`;
    if (book.cover_url && book.cover_url.startsWith('http')) return book.cover_url;
    if (book.isbn) return `https://covers.openlibrary.org/b/isbn/${Array.isArray(book.isbn)?book.isbn[0]:book.isbn}-M.jpg`;
    return `https://placehold.co/150x220/1a1a1e/FFFFFF?text=Sin+Portada`;
}

function getBestAuthor(book) {
    // 1. Si es de tu base de datos local
    if (book.author && typeof book.author === 'string') return book.author;
    
    // 2. Si viene de la Búsqueda (API Search)
    if (book.author_name && Array.isArray(book.author_name) && book.author_name.length > 0) {
        return book.author_name[0];
    }

    // 3. ¡ESTA ES LA CORRECCIÓN! Si viene de Categorías/Géneros (API Subjects)
    if (book.authors && Array.isArray(book.authors) && book.authors.length > 0) {
        return book.authors[0].name; 
    }

    return 'Autor desconocido';
}

function getBestCategory(book) {
    // 1. Si es local
    if (book.category && book.category.name) return book.category.name;

    // 2. Intentar sacar la categoría del propio libro (API)
    if (book.subject && Array.isArray(book.subject) && book.subject.length > 0) {
        return book.subject[0]; // Devuelve el primer tema como categoría
    }

    // 3. Si no, miramos qué tienes seleccionado en el menú desplegable
    const select = document.getElementById('category-select');
    if (select && select.value !== 'fiction' && select.selectedIndex >= 0) {
        // Limpiamos emojis y símbolos raros del texto
        return select.options[select.selectedIndex].text.replace(/[^\w\s\u00C0-\u00FF]/g, "").trim();
    }

    return 'General';
}

function openProductModal(index) {
    const book = currentBooks[index];
    const isLocal = book.created_at ? true : false;
    
    // Usamos las funciones corregidas aquí
    const cover = isLocal ? book.cover : getBestCover(book);
    const author = isLocal ? book.author : getBestAuthor(book); // <--- Aquí estaba el fallo antes
    
    const imgEl = document.getElementById('modal-img');
    imgEl.src = cover;
    imgEl.alt = `Portada completa de ${book.title}`; 
    
    document.getElementById('modal-title').innerText = book.title;
    document.getElementById('modal-author').innerText = author; // Ahora se verá bien
    
    const btn = document.getElementById('buy-btn');
    
    // Limpiamos eventos anteriores para que no se dupliquen compras
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.onclick = () => {
        prepararYAgregar(book);
        closeModal('product-modal');
        if(window.innerWidth < 768) {
             document.getElementById('drop-zone').classList.remove('translate-x-full');
        }
    };
    
    document.getElementById('product-modal').classList.remove('hidden');
}

async function logout() {
    if(!confirm("¿Cerrar sesión?")) return;
    const csrfToken = getCookie('XSRF-TOKEN');
    try { 
        await fetch('/logout', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': csrfToken } 
        }); 
        localStorage.removeItem('myBooks');
    } catch (e) { console.error(e); } 
    finally { window.location.href = '/'; }
}

async function handleSaveBook(e) {
    e.preventDefault();
    const id = document.getElementById('edit-book-id').value;
    const isEdit = id ? true : false;
    // ... (Logica de guardado igual que antes) ...
    // Para brevedad, usa la lógica ya existente de tu código anterior
    const data = {
        title: document.getElementById('edit-title').value,
        author: document.getElementById('edit-author').value,
        price: document.getElementById('edit-price').value,
        cover: document.getElementById('edit-cover').value,
        category_id: document.getElementById('edit-category').value
    };
    const url = isEdit ? `/api/books/${id}` : '/api/books';
    const method = isEdit ? 'PUT' : 'POST';
    const csrfToken = getCookie('XSRF-TOKEN');
    try {
        const res = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': csrfToken }, body: JSON.stringify(data) });
        if (res.ok) {
            document.getElementById('admin-modal').classList.add('hidden');
            loadLocalCatalog(); 
            alert("Guardado correctamente");
        } else { alert("Error al guardar"); }
    } catch (error) { console.error(error); }
}

async function deleteBook(id) {
    if(!confirm("¿Borrar libro?")) return;
    const csrfToken = getCookie('XSRF-TOKEN');
    try {
        const res = await fetch(`/api/books/${id}`, { method: 'DELETE', headers: { 'X-XSRF-TOKEN': csrfToken, 'Accept': 'application/json' } });
        if (res.ok) loadLocalCatalog();
    } catch (e) { console.error(e); }
}

window.onload = () => {
    loadUserProfile();
    searchByCategory(true); 
    loadCategoriesForAdmin();
};