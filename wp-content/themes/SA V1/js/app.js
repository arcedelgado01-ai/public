/**
 * Mi Tienda App - Soluciones Ardensa
 * Versión 5.5 - Selector unificado
 */

window.addEventListener('pageshow', function(e) {
    if (e.persisted) window.location.reload();
});

var cantidadTimers = {};
var carritoTimers = {};

// ========================================
// AGREGAR A COTIZACIÓN
// ========================================
function agregarAlCarrito(boton) {
    var id = boton.getAttribute('data-product-id');
    if (!id) return;

    mostrarSelectorCantidad(boton, id, 1);
    sumarBadge(1);
    mostrarNotificacion('¡Agregado a cotización!', 'success');

    ajax('mitienda_add_to_cart', 'product_id=' + id + '&quantity=1', function(ok, data) {
        if (ok) {
            setBadge(data.cart_count);
        } else {
            if (data && data.redirect) {
                window.location.href = data.redirect;
            } else {
                revertirBoton(id);
                mostrarNotificacion(data ? data.message : 'Error', 'error');
            }
        }
    });
}

// ========================================
// MOSTRAR SELECTOR DE CANTIDAD (TARJETAS)
// ========================================
function mostrarSelectorCantidad(boton, id, qty) {
    boton.parentElement.innerHTML =
        '<div class="qty-inline-selector" data-product-id="' + id + '">' +
            '<button type="button" class="qty-inline-btn" onclick="cambiarCantidadInline(this,-1)">−</button>' +
            '<span class="qty-inline-value">' + qty + '</span>' +
            '<button type="button" class="qty-inline-btn" onclick="cambiarCantidadInline(this,1)">+</button>' +
        '</div>';
}

// ========================================
// CAMBIAR CANTIDAD INLINE (TARJETAS)
// ========================================
function cambiarCantidadInline(btn, cambio) {
    var selector = btn.closest('.qty-inline-selector');
    var id = selector.getAttribute('data-product-id');
    var span = selector.querySelector('.qty-inline-value');
    var actual = parseInt(span.textContent);
    var nueva = actual + cambio;

    if (nueva > 99) nueva = 99;

    if (nueva <= 0) {
        span.textContent = '0';
        eliminarProductoInline(id, selector);
        return;
    }

    span.textContent = nueva;

    if (cantidadTimers[id]) clearTimeout(cantidadTimers[id]);

    cantidadTimers[id] = setTimeout(function() {
        ajax('mitienda_update_cart_by_product', 'product_id=' + id + '&quantity=' + nueva, function(ok, data) {
            if (ok) setBadge(data.cart_count);
        });
    }, 400);
}

// ========================================
// ELIMINAR PRODUCTO INLINE
// ========================================
function eliminarProductoInline(id, selector) {
    selector.parentElement.innerHTML =
        '<button type="button" class="btn-add-cart" data-product-id="' + id + '" onclick="agregarAlCarrito(this);return false;">Agregar a cotización</button>';

    restarBadge(1);

    ajax('mitienda_remove_by_product', 'product_id=' + id, function(ok, data) {
        if (ok) setBadge(data.cart_count);
    });
}

// ========================================
// REVERTIR BOTÓN SI FALLA
// ========================================
function revertirBoton(id) {
    var el = document.querySelector('.qty-inline-selector[data-product-id="' + id + '"]');
    if (el) {
        el.parentElement.innerHTML =
            '<button type="button" class="btn-add-cart" data-product-id="' + id + '" onclick="agregarAlCarrito(this);return false;">Agregar a cotización</button>';
    }
}

// ========================================
// CARRITO: Cambiar cantidad con selector inline
// ========================================
function cambiarCantidadCarritoInline(btn, cambio) {
    var selector = btn.closest('.qty-inline-selector');
    var key = selector.getAttribute('data-cart-key');
    var span = selector.querySelector('.qty-inline-value');
    var actual = parseInt(span.textContent);
    var nueva = actual + cambio;

    if (nueva > 99) nueva = 99;

    if (nueva <= 0) {
        eliminarDelCarrito(key);
        return;
    }

    span.textContent = nueva;

    if (carritoTimers[key]) clearTimeout(carritoTimers[key]);

    carritoTimers[key] = setTimeout(function() {
        ajax('mitienda_update_cart', 'cart_item_key=' + key + '&quantity=' + nueva, function(ok, data) {
            if (ok) {
                setBadge(data.cart_count);
                actualizarResumen(data.cart_count);
            }
        });
    }, 400);
}

// ========================================
// CARRITO: Eliminar producto
// ========================================
function eliminarDelCarrito(key) {
    var item = document.getElementById('cart-item-' + key);
    if (item) {
        item.style.transition = 'all 0.3s';
        item.style.opacity = '0';
        item.style.height = '0';
        item.style.overflow = 'hidden';
        item.style.padding = '0';
    }

    ajax('mitienda_remove_from_cart', 'cart_item_key=' + key, function(ok, data) {
        if (ok) {
            setBadge(data.cart_count);
            actualizarResumen(data.cart_count);
            setTimeout(function() {
                if (item) item.remove();
                if (data.is_empty) location.reload();
            }, 300);
            mostrarNotificacion('Producto eliminado', 'success');
        }
    });
}

// ========================================
// ACTUALIZAR RESUMEN
// ========================================
function actualizarResumen(count) {
    var el = document.querySelector('.cart-item-count');
    if (el) el.textContent = count + ' producto' + (count !== 1 ? 's' : '');
    var s = document.getElementById('summary-count');
    if (s) s.textContent = count;
}

// ========================================
// BADGE
// ========================================
function setBadge(count) {
    var b = document.getElementById('cart-badge');
    var bd = document.getElementById('cart-badge-desktop');
    if (b) {
        b.textContent = count;
        b.style.display = count > 0 ? 'flex' : 'none';
    }
    if (bd) {
        bd.textContent = count;
        bd.style.display = count > 0 ? 'flex' : 'none';
    }
}
function sumarBadge(n) {
    var b = document.getElementById('cart-badge');
    if (!b) return;
    var t = (parseInt(b.textContent) || 0) + n;
    b.textContent = t;
    b.style.display = 'flex';
    b.style.transform = 'scale(1.4)';
    setTimeout(function() { b.style.transform = 'scale(1)'; }, 200);
}

function restarBadge(n) {
    var b = document.getElementById('cart-badge');
    if (!b) return;
    var t = (parseInt(b.textContent) || 0) - n;
    if (t <= 0) { b.style.display = 'none'; b.textContent = '0'; }
    else { b.textContent = t; }
}

// ========================================
// NOTIFICACIÓN
// ========================================
function mostrarNotificacion(msg, tipo) {
    var n = document.getElementById('cart-notification');
    var t = document.getElementById('cart-notification-text');
    if (!n || !t) return;

    n.style.background = tipo === 'success' ? '#4CAF50' : tipo === 'error' ? '#f44336' : '#2196F3';
    t.textContent = (tipo === 'success' ? '✓ ' : tipo === 'error' ? '✗ ' : 'ℹ ') + msg;

    n.style.display = 'block';
    n.style.transform = 'translateY(-100%)';
    n.style.transition = 'transform 0.3s';

    setTimeout(function() { n.style.transform = 'translateY(0)'; }, 10);
    setTimeout(function() {
        n.style.transform = 'translateY(-100%)';
        setTimeout(function() { n.style.display = 'none'; }, 300);
    }, 2500);
}

// ========================================
// AJAX HELPER
// ========================================
function ajax(action, params, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', mitienda_ajax.ajax_url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            try { var r = JSON.parse(xhr.responseText); callback(r.success, r.data); }
            catch(e) { callback(false, null); }
        } else { callback(false, null); }
    };
    xhr.onerror = function() { callback(false, null); };
    xhr.send('action=' + action + '&' + params + '&nonce=' + mitienda_ajax.nonce);
}

// ========================================
// CARRUSEL DE BANNERS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    var slides = document.getElementById('bannerSlides');
    var dots = document.querySelectorAll('.banner-dot');
    var cur = 0, total = dots.length;

    if (total > 0 && slides) {
        function go() { cur = (cur + 1) % total; upd(); }
        function upd() {
            slides.style.transform = 'translateX(-' + (cur * 100) + '%)';
            dots.forEach(function(d, i) { d.classList.toggle('active', i === cur); });
        }
        var auto = setInterval(go, 4000);
        dots.forEach(function(d, i) {
            d.addEventListener('click', function() { cur = i; upd(); clearInterval(auto); auto = setInterval(go, 4000); });
        });
        var carousel = document.getElementById('bannerCarousel');
        if (carousel) {
            var sx = 0;
            carousel.addEventListener('touchstart', function(e) { sx = e.changedTouches[0].screenX; });
            carousel.addEventListener('touchend', function(e) {
                var diff = sx - e.changedTouches[0].screenX;
                if (diff > 50) cur = (cur + 1) % total;
                else if (diff < -50) cur = (cur - 1 + total) % total;
                upd(); clearInterval(auto); auto = setInterval(go, 4000);
            });
        }
    }

    // Header ocultar al bajar
    var lastScroll = 0, header = document.querySelector('.header-app');
    if (header) {
        window.addEventListener('scroll', function() {
            var s = window.pageYOffset;
            header.style.transition = 'transform 0.3s';
            header.style.transform = (s > lastScroll && s > 150) ? 'translateY(-100%)' : 'translateY(0)';
            lastScroll = s;
        });
    }
});

// ========================================
// GALERÍA DE PRODUCTO
// ========================================
var gpSlide = 0, gpTotal = 0;

document.addEventListener('DOMContentLoaded', function() {
    var g = document.getElementById('productGallery');
    if (!g) return;
    gpTotal = g.children.length;
    var sx = 0;
    g.addEventListener('touchstart', function(e) { sx = e.changedTouches[0].screenX; });
    g.addEventListener('touchend', function(e) {
        var diff = sx - e.changedTouches[0].screenX;
        if (diff > 50 && gpSlide < gpTotal - 1) gpSlide++;
        else if (diff < -50 && gpSlide > 0) gpSlide--;
        updateGallery();
    });
});

function updateGallery() {
    var g = document.getElementById('productGallery');
    var c = document.getElementById('currentImage');
    if (g) g.style.transform = 'translateX(-' + (gpSlide * 100) + '%)';
    if (c) c.textContent = gpSlide + 1;
    document.querySelectorAll('.thumb').forEach(function(t, i) { t.classList.toggle('active', i === gpSlide); });
}

function irASlide(i) { gpSlide = i; updateGallery(); }

// ========================================
// CANTIDAD EN PÁGINA DE PRODUCTO
// ========================================
function cambiarCantidad(c) {
    var el = document.getElementById('product-qty');
    if (!el) return;
    var q = parseInt(el.textContent) + c;
    if (q < 1) q = 1;
    if (q > 99) q = 99;
    el.textContent = q;
}

// ========================================
// MOSTRAR DESCRIPCIÓN
// ========================================
function mostrarDescripcion(btn) {
    var d = document.getElementById('fullDescription');
    var f = document.getElementById('descFade');
    if (d.style.maxHeight === '150px') {
        d.style.maxHeight = 'none';
        if (f) f.style.display = 'none';
        btn.textContent = 'Ver menos ▲';
    } else {
        d.style.maxHeight = '150px';
        if (f) f.style.display = 'block';
        btn.textContent = 'Ver más ▼';
    }
}

// ========================================
// ENVIAR COTIZACIÓN
// ========================================
function enviarCotizacion() {
    var name = document.getElementById('quote-name').value.trim();
    var email = document.getElementById('quote-email').value.trim();
    var phone = document.getElementById('quote-phone').value.trim();
    var notes = document.getElementById('quote-notes').value.trim();
    var method = document.querySelector('input[name="contact_method"]:checked').value;

    if (!name) { mostrarNotificacion('Ingresa tu nombre', 'error'); return; }
    if (!email && !phone) { mostrarNotificacion('Ingresa correo o teléfono', 'error'); return; }

    var list = '';
    document.querySelectorAll('.cart-item').forEach(function(item) {
        var n = item.querySelector('.cart-item-name');
        var q = item.querySelector('.qty-inline-value');
        if (n && q) list += '• ' + n.textContent.trim() + ' (x' + q.textContent.trim() + ')\n';
    });

    var msg = '📋 *SOLICITUD DE COTIZACIÓN*\n\n';
    msg += '*Nombre:* ' + name + '\n';
    if (email) msg += '*Email:* ' + email + '\n';
    if (phone) msg += '*Teléfono:* ' + phone + '\n';
    msg += '*Contactar por:* ' + (method === 'whatsapp' ? 'WhatsApp' : 'Correo electrónico') + '\n';
    msg += '\n*Productos:*\n' + list;
    if (notes) msg += '\n*Notas:* ' + notes;

    window.whatsappMsg = msg;

    var btn = document.querySelector('.btn-checkout');
    btn.textContent = '⏳ Procesando...';
    btn.disabled = true;

    ajax('mitienda_crear_pedido',
        'name=' + encodeURIComponent(name) +
        '&email=' + encodeURIComponent(email) +
        '&phone=' + encodeURIComponent(phone) +
        '&notes=' + encodeURIComponent(notes) +
        '&method=' + method,
    function(ok, data) {
        btn.textContent = '📩 Solicitar cotización';
        btn.disabled = false;

        if (ok) {
            setBadge(0);
            document.getElementById('modalExito').style.display = 'flex';
        } else {
            mostrarNotificacion('Error al procesar. Intenta de nuevo.', 'error');
        }
    });
}

// ========================================
// ABRIR WHATSAPP DESDE MODAL
// ========================================
function abrirWhatsApp() {
    var num = '521234567890'; // CAMBIA ESTE NÚMERO
    var msg = window.whatsappMsg || 'Hola, acabo de enviar una cotización';
    window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(msg), '_blank');
}

// ========================================
// FEED: Filtrar
// ========================================
function cambiarFeed(tab, red) {
    document.querySelectorAll('.feed-tab').forEach(function(t) { t.classList.remove('active'); });
    tab.classList.add('active');
    document.querySelectorAll('.feed-item').forEach(function(item) {
        item.style.display = (red === 'todos' || item.getAttribute('data-red') === red) ? '' : 'none';
    });
}

// ========================================
// REEMPLAZAR SELECTOR VIEJO EN CARRITO
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    var selectoresViejos = document.querySelectorAll('.cart-qty-selector');
    
    selectoresViejos.forEach(function(viejo) {
        var botones = viejo.querySelectorAll('button');
        var span = viejo.querySelector('span');
        
        if (!botones.length || !span) return;
        
        // Extraer el cart_item_key del onclick del botón
        var onclickStr = botones[0].getAttribute('onclick') || '';
        var match = onclickStr.match(/actualizarCantidadCarrito\('([^']+)'/);
        var cartKey = match ? match[1] : '';
        var cantidad = parseInt(span.textContent) || 1;
        
        // Buscar el product_id del item más cercano
        var cartItem = viejo.closest('.cart-item');
        var productId = '';
        if (cartItem) {
            var img = cartItem.querySelector('[data-product-id]');
            if (img) productId = img.getAttribute('data-product-id');
        }
        
        // Crear selector nuevo
        var nuevo = document.createElement('div');
        nuevo.className = 'qty-inline-selector';
        nuevo.setAttribute('data-product-id', productId);
        nuevo.setAttribute('data-cart-key', cartKey);
        nuevo.innerHTML = 
            '<button type="button" class="qty-inline-btn" onclick="cambiarCantidadCarritoInline(this,-1)">−</button>' +
            '<span class="qty-inline-value">' + cantidad + '</span>' +
            '<button type="button" class="qty-inline-btn" onclick="cambiarCantidadCarritoInline(this,1)">+</button>';
        
        // Reemplazar
        viejo.parentNode.replaceChild(nuevo, viejo);
    });
});


// ========================================
// CREAR CUENTA
// ========================================
function crearCuenta() {
    var email = document.getElementById('registro-email').value.trim();
    var btn = document.getElementById('btn-crear');
    var msg = document.getElementById('registro-msg');

    if (!email) { msg.innerHTML = '<span style="color:#f44336;">Ingresa tu correo</span>'; return; }

    btn.textContent = 'Creando...';
    btn.disabled = true;

    ajax('ardensa_crear_cuenta', 'email=' + encodeURIComponent(email), function(ok, data) {
        btn.textContent = 'Crear cuenta';
        btn.disabled = false;
        if (ok) {
            msg.innerHTML = '<span style="color:#4CAF50;">' + data.message + '</span>';
            setTimeout(function() {
                window.location.href = window.location.pathname;
            }, 3000);
        } else {
            msg.innerHTML = '<span style="color:#f44336;">' + (data ? data.message : 'Error') + '</span>';
        }
    });
}

// ========================================
// RECUPERAR CONTRASEÑA
// ========================================
function recuperarPassword() {
    var email = document.getElementById('recuperar-email').value.trim();
    var btn = document.getElementById('btn-recuperar');
    var msg = document.getElementById('recuperar-msg');

    if (!email) { msg.innerHTML = '<span style="color:#f44336;">Ingresa tu correo</span>'; return; }

    btn.textContent = 'Enviando...';
    btn.disabled = true;

    ajax('ardensa_recuperar_password', 'email=' + encodeURIComponent(email), function(ok, data) {
        btn.textContent = 'Enviar nueva contraseña';
        btn.disabled = false;
        if (ok) {
            msg.innerHTML = '<span style="color:#4CAF50;">' + data.message + '</span>';
        } else {
            msg.innerHTML = '<span style="color:#f44336;">' + (data ? data.message : 'Error') + '</span>';
        }
    });
}

// ========================================
// SELECCIONAR MÉTODO DE CONTACTO
// ========================================
function seleccionarMetodo(method) {
    var wa = document.getElementById('method-whatsapp');
    var em = document.getElementById('method-email');
    
    if (method === 'whatsapp') {
        wa.style.border = '2px solid #cc2229';
        wa.style.background = '#fff5f5';
        em.style.border = '2px solid #e0e0e0';
        em.style.background = '#ffffff';
        wa.querySelector('input').checked = true;
    } else {
        em.style.border = '2px solid #cc2229';
        em.style.background = '#fff5f5';
        wa.style.border = '2px solid #e0e0e0';
        wa.style.background = '#ffffff';
        em.querySelector('input').checked = true;
    }
}

// ========================================
// DROPDOWN DE CATEGORÍAS
// ========================================

document.addEventListener('DOMContentLoaded', function () {

    var buttons = document.querySelectorAll('.category-dropdown-toggle');

    buttons.forEach(function (button) {

        var wrapper = button.closest('.category-item-wrapper');

        if (!wrapper) {
            return;
        }

        // Guardamos la referencia ORIGINAL al dropdown.
        // Esto es importante porque después lo moveremos al body.
        var children = wrapper.querySelector(
            ':scope > .category-dropdown-children'
        );

        if (!children) {
            return;
        }

        // Guardar referencias permanentemente en el botón
        button._categoryDropdown = children;
        children._categoryButton = button;
        children._categoryOriginalParent = children.parentNode;
        children._categoryOriginalNextSibling = children.nextSibling;


        // ========================================
        // CLICK EN LA FLECHA
        // ========================================

        button.addEventListener('click', function (event) {

            event.preventDefault();
            event.stopPropagation();

            var dropdown = button._categoryDropdown;

            if (!dropdown) {
                return;
            }

            var isOpen =
                button.getAttribute('aria-expanded') === 'true';


            // ========================================
            // SI ESTÁ ABIERTO → CERRAR
            // ========================================

            if (isOpen) {

                closeDropdown(button, dropdown);

                return;
            }


            // ========================================
            // CERRAR OTROS
            // ========================================

            document
                .querySelectorAll(
                    '.category-dropdown-toggle[aria-expanded="true"]'
                )
                .forEach(function (otherButton) {

                    if (otherButton === button) {
                        return;
                    }

                    var otherDropdown =
                        otherButton._categoryDropdown;

                    if (otherDropdown) {
                        closeDropdown(
                            otherButton,
                            otherDropdown
                        );
                    }

                });


            // ========================================
            // ABRIR
            // ========================================

            openDropdown(button, dropdown);

        });

    });


    // ========================================
    // ABRIR DROPDOWN
    // ========================================

    function openDropdown(button, dropdown) {

        var buttonRect =
            button.getBoundingClientRect();

        var header =
            document.querySelector('.header-desktop');

        var headerBottom = 0;

        if (header) {

            var headerRect =
                header.getBoundingClientRect();

            headerBottom =
                headerRect.bottom;
        }


        // ========================================
        // MOVER AL BODY
        // ========================================

        document.body.appendChild(dropdown);


        // ========================================
        // MOSTRAR
        // ========================================

        dropdown.hidden = false;

        button.setAttribute(
            'aria-expanded',
            'true'
        );


        // ========================================
        // USAR POSITION: FIXED
        // ========================================
        /*
         * Al usar fixed:
         *
         * - no lo recorta el scroll horizontal
         * - no lo recorta el banner
         * - no depende del z-index de los padres
         */

        dropdown.style.position = 'fixed';

        dropdown.style.zIndex = '9000';

        dropdown.style.display = 'block';


        // ========================================
        // POSICIÓN HORIZONTAL
        // ========================================

        dropdown.style.left =
            buttonRect.left + 'px';


        // ========================================
        // POSICIÓN VERTICAL
        // ========================================

        var top =
            buttonRect.bottom + 8;


        /*
         * El header siempre tiene prioridad.
         *
         * Si el dropdown intentara quedar dentro
         * del header, lo colocamos debajo.
         */

        if (top < headerBottom) {

            top =
                headerBottom + 8;
        }


        dropdown.style.top =
            top + 'px';


        // ========================================
        // MARCADOR
        // ========================================

        dropdown._categoryOpen = true;

    }


    // ========================================
    // CERRAR DROPDOWN
    // ========================================

    function closeDropdown(button, dropdown) {

        button.setAttribute(
            'aria-expanded',
            'false'
        );


        dropdown.hidden = true;


        // ========================================
        // DEVOLVER A SU LUGAR ORIGINAL
        // ========================================

        var parent =
            dropdown._categoryOriginalParent;

        var nextSibling =
            dropdown._categoryOriginalNextSibling;


        if (parent) {

            if (
                nextSibling &&
                nextSibling.parentNode === parent
            ) {

                parent.insertBefore(
                    dropdown,
                    nextSibling
                );

            } else {

                parent.appendChild(
                    dropdown
                );

            }

        }


        // ========================================
        // LIMPIAR ESTILOS
        // ========================================

        dropdown.style.position = '';
        dropdown.style.zIndex = '';
        dropdown.style.display = '';
        dropdown.style.left = '';
        dropdown.style.top = '';

        dropdown._categoryOpen = false;

    }


    // ========================================
    // CLICK FUERA
    // ========================================

    document.addEventListener('click', function (event) {

        var clickedButton =
            event.target.closest(
                '.category-dropdown-toggle'
            );

        var clickedDropdown =
            event.target.closest(
                '.category-dropdown-children'
            );


        // Click en flecha
        if (clickedButton) {
            return;
        }


        // Click dentro del dropdown
        if (clickedDropdown) {
            return;
        }


        // Cerrar todos
        document
            .querySelectorAll(
                '.category-dropdown-toggle[aria-expanded="true"]'
            )
            .forEach(function (button) {

                var dropdown =
                    button._categoryDropdown;

                if (dropdown) {

                    closeDropdown(
                        button,
                        dropdown
                    );

                }

            });

    });


    // ========================================
    // SCROLL DE LA PÁGINA
    // ========================================

    window.addEventListener('scroll', function () {

        document
            .querySelectorAll(
                '.category-dropdown-toggle[aria-expanded="true"]'
            )
            .forEach(function (button) {

                var dropdown =
                    button._categoryDropdown;

                if (!dropdown) {
                    return;
                }


                var rect =
                    button.getBoundingClientRect();


                var header =
                    document.querySelector(
                        '.header-desktop'
                    );

                var headerBottom = 0;

                if (header) {

                    headerBottom =
                        header.getBoundingClientRect().bottom;
                }


                var top =
                    rect.bottom + 8;


                if (top < headerBottom) {

                    top =
                        headerBottom + 8;
                }


                dropdown.style.top =
                    top + 'px';

                dropdown.style.left =
                    rect.left + 'px';

            });

    });

});



document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENTOS
       ========================================================= */

    const carousel   = document.querySelector('.categories-container');
    const leftArrow  = document.querySelector('.categories-arrow-left');
    const rightArrow = document.querySelector('.categories-arrow-right');

    if (!carousel || !leftArrow || !rightArrow) return;


    /* =========================================================
       CONFIGURACIÓN
       ========================================================= */

    const CONFIG = {
        // Duración base del snap con flechas (ms)
        DURATION: 550,

        // Fricción del momentum al soltar el drag (0-1, más alto = frena antes)
        FRICTION: 0.94,

        // Velocidad mínima para seguir con momentum (px/frame)
        MIN_VELOCITY: 0.4,

        // Fuerza del rubber band en los extremos (0-1)
        RUBBER: 0.35,

        // Velocidad de la rueda del mouse
        WHEEL_SPEED: 1.0,

        // Umbral de distancia para distinguir click de drag (px)
        CLICK_THRESHOLD: 6,

        // Duración del snap final (ms)
        SNAP_DURATION: 380
    };


    /* =========================================================
       ESTADO
       ========================================================= */

    let isAnimating     = false;
    let isDragging      = false;
    let dragStartX      = 0;
    let dragStartScroll = 0;
    let lastX           = 0;
    let lastTime        = 0;
    let velocity        = 0;
    let dragDistance    = 0;
    let momentumRAF     = null;
    let animRAF         = null;


    /* =========================================================
       UTILIDADES
       ========================================================= */

    function clamp(v, min, max) {
        return Math.max(min, Math.min(v, max));
    }

    function getMaxScroll() {
        return carousel.scrollWidth - carousel.clientWidth;
    }

    // Easing premium: arranca rápido, frena muy suave
    function easeOutExpo(t) {
        return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
    }


    /* =========================================================
       ACTUALIZAR FLECHAS
       ========================================================= */

    function updateArrows() {
        const scrollLeft = carousel.scrollLeft;
        const maxScroll  = getMaxScroll();

        leftArrow.classList.toggle('is-hidden', scrollLeft <= 5);
        rightArrow.classList.toggle('is-hidden', scrollLeft >= maxScroll - 5);
    }


    /* =========================================================
       CÁLCULO DE PASO (una tarjeta + gap)
       ========================================================= */

    function getScrollAmount() {
        const card = carousel.querySelector('.category-item-wrapper');
        if (!card) return carousel.clientWidth;

        const cardWidth = card.getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(carousel).gap) || 0;
        return cardWidth + gap;
    }


    /* =========================================================
       ANIMACIÓN PRINCIPAL (con cancelación)
       ========================================================= */

    function animateTo(target, duration) {
        cancelAnimationFrame(animRAF);

        const start    = carousel.scrollLeft;
        const distance = target - start;

        if (Math.abs(distance) < 0.5) {
            carousel.scrollLeft = target;
            isAnimating = false;
            updateArrows();
            return;
        }

        isAnimating = true;
        const startTime = performance.now();

        function frame(now) {
            const t = clamp((now - startTime) / duration, 0, 1);
            const eased = easeOutExpo(t);

            carousel.scrollLeft = start + distance * eased;

            if (t < 1) {
                animRAF = requestAnimationFrame(frame);
            } else {
                carousel.scrollLeft = target;
                isAnimating = false;
                updateArrows();
            }
        }

        animRAF = requestAnimationFrame(frame);
    }


    /* =========================================================
       SNAP A LA TARJETA MÁS CERCANA
       ========================================================= */

    function snapToNearest() {
        const amount    = getScrollAmount();
        const current   = carousel.scrollLeft;
        const maxScroll = getMaxScroll();

        let target = Math.round(current / amount) * amount;
        target = clamp(target, 0, maxScroll);

        animateTo(target, CONFIG.SNAP_DURATION);
    }


    /* =========================================================
       CANCELAR MOMENTUM
       ========================================================= */

    function cancelMomentum() {
        if (momentumRAF) {
            cancelAnimationFrame(momentumRAF);
            momentumRAF = null;
        }
    }


    /* =========================================================
       FLECHAS
       ========================================================= */

    function moveBy(direction) {
        if (isAnimating || isDragging) return;

        cancelMomentum();

        const amount    = getScrollAmount();
        const current   = carousel.scrollLeft;
        const maxScroll = getMaxScroll();

        let target = current + amount * direction;
        target = clamp(target, 0, maxScroll);

        // Duración adaptativa: si el recorrido es corto, más rápida
        const distance = Math.abs(target - current);
        const duration = CONFIG.DURATION * (0.6 + 0.4 * (distance / amount));

        animateTo(target, duration);
    }

    rightArrow.addEventListener('click', () => moveBy(1));
    leftArrow.addEventListener('click', () => moveBy(-1));


    /* =========================================================
       DRAG 1:1 + MOMENTUM
       ========================================================= */

    function onPointerDown(e) {
        if (e.button !== undefined && e.button !== 0) return;

        isDragging      = true;
        dragStartX      = e.clientX;
        dragStartScroll = carousel.scrollLeft;
        lastX           = e.clientX;
        lastTime        = performance.now();
        velocity        = 0;
        dragDistance    = 0;

        cancelAnimationFrame(animRAF);
        cancelMomentum();

        carousel.classList.add('is-dragging');

        /* ✅ NO capturamos el pointer acá.
           Solo lo haremos cuando el movimiento
           confirme que es un drag real. */
        carousel._pointerCaptured = false;
    }

    function onPointerMove(e) {
        if (!isDragging) return;

        const now = performance.now();
        const dt  = now - lastTime;

        const dx = e.clientX - lastX;

        dragDistance += Math.abs(dx);

        /* ✅ Capturamos el pointer SOLO cuando ya es un drag real.
           Antes de eso, el click puede viajar libremente al link. */
        if (dragDistance > CONFIG.CLICK_THRESHOLD && !carousel._pointerCaptured) {
            carousel._pointerCaptured = true;
            try {
                carousel.setPointerCapture(e.pointerId);
            } catch (err) {}
        }

        /* ✅ Solo preventDefault si ya estamos en modo drag real */
        if (dragDistance > CONFIG.CLICK_THRESHOLD) {
            e.preventDefault();
        }

        const rawTarget = dragStartScroll - (e.clientX - dragStartX);

        const maxScroll = getMaxScroll();
        let finalScroll = rawTarget;

        // Rubber band en los extremos
        if (rawTarget < 0) {
            finalScroll = rawTarget * CONFIG.RUBBER;
        } else if (rawTarget > maxScroll) {
            finalScroll = maxScroll + (rawTarget - maxScroll) * CONFIG.RUBBER;
        }

        carousel.scrollLeft = finalScroll;

        if (dt > 0) {
            velocity = dx / dt;
        }

        lastX    = e.clientX;
        lastTime = now;
    }

    function onPointerUp(e) {
        if (!isDragging) return;

        isDragging = false;
        carousel.classList.remove('is-dragging');

        /* ✅ Liberar el pointer capture si lo habíamos tomado */
        if (carousel._pointerCaptured && e && e.pointerId !== undefined) {
            try {
                carousel.releasePointerCapture(e.pointerId);
            } catch (err) {}
        }
        carousel._pointerCaptured = false;

        /* Si apenas se movió, es un CLICK → no hacemos nada más */
        if (dragDistance < CONFIG.CLICK_THRESHOLD) {
            velocity = 0;
            return;
        }

        /* Fue un drag → bloqueamos el click que viene después */
        carousel._suppressClick = true;

        // Convertimos px/ms → px/frame (~16.67ms)
        let v = velocity * 16.67;

        // Si soltó muy lento, snap a la tarjeta más cercana
        if (Math.abs(v) < CONFIG.MIN_VELOCITY) {
            snapToNearest();
            return;
        }

        function momentum() {
            v *= CONFIG.FRICTION;

            const maxScroll = getMaxScroll();
            let next = carousel.scrollLeft - v;

            // Rubber band + rebote al llegar al límite
            if (next < 0) {
                next = next * CONFIG.RUBBER;
                carousel.scrollLeft = next;
                animateTo(0, 400);
                return;
            }
            if (next > maxScroll) {
                next = maxScroll + (next - maxScroll) * CONFIG.RUBBER;
                carousel.scrollLeft = next;
                animateTo(maxScroll, 400);
                return;
            }

            carousel.scrollLeft = next;

            if (Math.abs(v) > CONFIG.MIN_VELOCITY) {
                momentumRAF = requestAnimationFrame(momentum);
            } else {
                snapToNearest();
            }
        }

        momentumRAF = requestAnimationFrame(momentum);
    }

    carousel.addEventListener('pointerdown', onPointerDown);
    carousel.addEventListener('pointermove', onPointerMove);
    carousel.addEventListener('pointerup', onPointerUp);
    carousel.addEventListener('pointercancel', onPointerUp);
    /* ❌ NO usamos pointerleave: rompería el flujo del click en desktop */


    /* =========================================================
       SUPRIMIR CLICK DESPUÉS DE UN DRAG
       ========================================================= */

    carousel.addEventListener('click', function (e) {
        if (carousel._suppressClick) {
            e.preventDefault();
            e.stopPropagation();
            carousel._suppressClick = false;
        }
    }, true);


    /* =========================================================
       RUEDA DEL MOUSE (normalizada)
       ========================================================= */

    carousel.addEventListener('wheel', function (e) {

        // Solo interceptamos si el scroll es horizontal real
        // o si el usuario usa shift (típico para scroll horizontal)
        const isHorizontalIntent =
            Math.abs(e.deltaX) > Math.abs(e.deltaY) || e.shiftKey;

        if (!isHorizontalIntent) return;

        e.preventDefault();
        cancelMomentum();
        cancelAnimationFrame(animRAF);

        // Normalizamos: deltaMode 0 = píxeles, 1 = líneas, 2 = páginas
        let delta = e.deltaX;
        if (e.shiftKey && Math.abs(e.deltaX) < Math.abs(e.deltaY)) {
            delta = e.deltaY;
        }
        if (e.deltaMode === 1) delta *= 16;
        if (e.deltaMode === 2) delta *= carousel.clientWidth;

        carousel.scrollLeft += delta * CONFIG.WHEEL_SPEED;

        // Snap diferido: después de que el usuario deja de scrollear
        clearTimeout(carousel._wheelSnapTimer);
        carousel._wheelSnapTimer = setTimeout(snapToNearest, 140);

    }, { passive: false });


    /* =========================================================
       TECLADO (accesibilidad premium)
       ========================================================= */

    carousel.setAttribute('tabindex', '0');

    carousel.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') { e.preventDefault(); moveBy(1); }
        if (e.key === 'ArrowLeft')  { e.preventDefault(); moveBy(-1); }
        if (e.key === 'Home')       { e.preventDefault(); animateTo(0, 500); }
        if (e.key === 'End')        { e.preventDefault(); animateTo(getMaxScroll(), 500); }
    });


    /* =========================================================
       SCROLL MANUAL
       ========================================================= */

    carousel.addEventListener('scroll', updateArrows, { passive: true });


    /* =========================================================
       RESIZE
       ========================================================= */

    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            cancelMomentum();
            cancelAnimationFrame(animRAF);
            snapToNearest();
            updateArrows();
        }, 120);
    });


    /* =========================================================
       CARGA DE IMÁGENES
       ========================================================= */

    carousel.querySelectorAll('img').forEach(function (img) {
        if (!img.complete) {
            img.addEventListener('load', updateArrows, { once: true });
        }
    });


    /* =========================================================
       INICIALIZACIÓN
       ========================================================= */

    updateArrows();
    requestAnimationFrame(updateArrows);

});