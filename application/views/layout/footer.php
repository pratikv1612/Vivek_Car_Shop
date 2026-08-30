        </div>
    </main>

    <section class="va-service-strip">
        <div class="container">
            <div class="va-service-row">
                <div><i class="bi bi-truck"></i><span>Same Day Product Delivery</span></div>
                <div><i class="bi bi-house-check"></i><span>100% Customer Satisfaction</span></div>
                <div><i class="bi bi-patch-check"></i><span>High And Access On Demand</span></div>
                <div><i class="bi bi-award"></i><span>100% Quality Car Accessories</span></div>
                <div><i class="bi bi-headset"></i><span>24/7 Support For Clients</span></div>
            </div>
        </div>
    </section>

    <footer class="va-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="va-footer-brand">
                        <img src="<?= base_url('/assets/image/logo.jpeg') ?>" alt="<?= html_escape($site_name ?? 'V Auto Spare') ?> Logo">
                        <span><?= html_escape($site_name ?? 'V Auto Spare') ?></span>
                    </div>
                    <p>Find verified car spare parts, accessories, and vehicle essentials from trusted sellers.</p>
                    <a class="va-footer-phone" href="<?= html_escape($whatsapp_url ?? '#') ?>" target="_blank"><i class="bi bi-telephone"></i> <?= html_escape($whatsapp_number ?? '91 2345 678') ?></a>
                    <div class="va-socials">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="<?= html_escape($whatsapp_url ?? '#') ?>" target="_blank"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h4>Resources</h4>
                    <a href="<?= base_url() ?>">Home</a>
                    <a href="<?= base_url('accessories') ?>">Shop</a>
                    <a href="<?= base_url('cars') ?>">Cars</a>
                    <a href="<?= base_url('register') ?>">Register</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h4>Support</h4>
                    <a href="<?= base_url('contact') ?>">Contact</a>
                    <a href="<?= base_url('login') ?>">Account</a>
                    <a href="<?= base_url('about') ?>">About Us</a>
                    <a href="<?= base_url('accessories') ?>">Online Payment</a>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h4>Store Info</h4>
                    <a href="<?= base_url('accessories') ?>">Best Seller</a>
                    <a href="<?= base_url('accessories') ?>">Top Rated Items</a>
                    <a href="<?= base_url('accessories') ?>">New Arrivals</a>
                    <a href="<?= base_url('accessories') ?>">Discount Products</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4>Subscribe</h4>
                    <p>Stay updated about upcoming events, releases, and exciting offers.</p>
                    <form class="va-subscribe" action="#" method="post">
                        <input type="email" placeholder="Email address" aria-label="Email address">
                        <button type="submit"><i class="bi bi-arrow-right"></i></button>
                    </form>
                </div>
            </div>
            <div class="va-footer-bottom">
                <span>Copyright &copy; <?= date('Y') ?> <?= html_escape($site_name ?? 'V Auto Spare') ?>. All Rights Reserved.</span>
                <span class="va-payments">Visa&nbsp;&nbsp; Mastercard&nbsp;&nbsp; PayPal&nbsp;&nbsp; Stripe</span>
            </div>
        </div>
    </footer>

    <a href="<?= html_escape($whatsapp_url ?? '#') ?>" target="_blank" class="whatsapp-float" title="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a>

    <div class="va-cart-drawer" id="cartDrawer" aria-hidden="true"><div class="va-drawer-head"><strong>Shopping Cart</strong><button type="button" class="close-drawer">×</button></div><div class="va-drawer-items"></div><div class="va-drawer-foot"><span>Subtotal: <b id="drawerTotal">&#8377; 0</b></span><div><a class="btn btn-dark" href="<?= base_url('cart') ?>">View Cart</a><a class="btn va-why-button" href="<?= base_url('checkout') ?>">Checkout</a></div></div></div><div class="va-shop-overlay" id="shopOverlay"></div>
    <div class="va-shop-modal" id="shopModal"><div class="va-modal-head"><strong id="shopModalTitle">Wishlist</strong><button type="button" class="close-modal">×</button></div><div class="va-modal-body" id="shopModalBody"></div><div class="va-modal-foot"><a id="shopModalPage" href="#">Open page</a><button type="button" class="close-modal">Continue shopping</button></div></div>
    <div class="va-quick-modal" id="quickModal"><button type="button" class="close-quick">×</button><img id="quickImage" src="" alt=""><div><span>Quick view</span><h2 id="quickName"></h2><strong id="quickPrice"></strong></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= base_url('assets/js/theme-toggle.js') ?>"></script>
    <script src="<?= base_url('assets/js/home-slider.js') ?>"></script>
    <script>
    (function () {
        var base = '<?= base_url() ?>';
        function overlay(show) { document.getElementById('shopOverlay').classList.toggle('show', show); }
        function setCounts(counts) { ['cart','wishlist','compare'].forEach(function(type){ var el=document.getElementById(type+'Count'); if(el) el.textContent=counts[type]||0; }); }
        function cartSummary(open) { $.post(base+'shop/summary', {}, function(res){ if(!res.status) return; setCounts(res.counts); $('#drawerTotal').html('&#8377; '+Number(res.total).toLocaleString('en-IN')); var html=''; res.items.forEach(function(item){ html+='<div class="va-drawer-item"><img src="'+(item.primary_image||base+'assets/images/placeholder-product.svg')+'" alt="">'+'<div><b>'+item.name+'</b><span>'+item.quantity+' × &#8377; '+Number(item.price).toLocaleString('en-IN')+'</span></div><button class="shop-action" data-action="remove-cart" data-product-id="'+item.id+'">×</button></div>'; }); $('.va-drawer-items').html(html || '<p class="va-drawer-empty">Your cart is empty.</p>'); if(open){ $('#cartDrawer').addClass('show'); overlay(true); } }, 'json'); }
        function showListModal(type) { var label=type.charAt(0).toUpperCase()+type.slice(1); $('#shopModalTitle').text(label); $('#shopModalBody').html('<p>Your product was added to '+label.toLowerCase()+'.</p>'); $('#shopModalPage').attr('href', base+type).text('Open '+label+' page'); $('#shopModal').addClass('show'); overlay(true); }
        function listSummary(type, open) { $.post(base+'shop/summary', {}, function(res){ if(!res.status) return; setCounts(res.counts); if(open){ showListModal(type); } }, 'json'); }
        $(document).on('click','.shop-action',function(e){ e.preventDefault(); e.stopPropagation(); var button=$(this), action=button.data('action'), id=button.data('product-id'), remove=String(action).indexOf('remove-')===0, type=remove?String(action).replace('remove-',''):action; $.post(base+'shop/'+(remove?'remove/':'add/')+type,{product_id:id},function(res){ if(!res.status){ alert(res.message); return; } setCounts(res.counts); if(remove){ if(window.location.pathname.match(/cart|wishlist|compare/)) location.reload(); else cartSummary(false); return; } if(type==='cart') cartSummary(true); else showListModal(type); },'json'); });
        $(document).on('click','.cart-quantity',function(){ var id=$(this).data('product-id'), current=parseInt($(this).siblings('b').text(),10), qty=Math.max(1,current+parseInt($(this).data('change'),10)); $.post(base+'shop/quantity',{product_id:id,quantity:qty},function(){location.reload();},'json'); });
        $(document).on('click','.shop-quick-view',function(){ $('#quickName').text($(this).data('name')); $('#quickPrice').html($(this).data('price')); $('#quickImage').attr('src',$(this).data('image')); $('#quickModal').addClass('show'); overlay(true); });
        $(document).on('click','.car-save-action',function(e){ e.preventDefault(); e.stopPropagation(); var b=$(this), icon=b.find('i'); $.post(base+'cars/toggle_save',{car_id:b.data('car-id')},function(res){ if(!res.status){ alert(res.message); return; } if(res.saved){ b.addClass('is-saved'); icon.addClass('bi-heart-fill').removeClass('bi-heart'); } else { b.removeClass('is-saved'); icon.addClass('bi-heart').removeClass('bi-heart-fill'); } },'json'); });
        $(document).on('click','.va-header-cart-trigger',function(){ cartSummary(true); });
        $(document).on('click','.va-header-shop-link[title="Wishlist"]',function(e){ e.preventDefault(); window.location.href = base + 'wishlist'; });
        $(document).on('click','.va-header-shop-link[title="Compare"]',function(e){ e.preventDefault(); window.location.href = base + 'compare'; });
        $(document).on('click','.close-drawer,.close-modal,.close-quick,#shopOverlay',function(){ $('#cartDrawer,#shopModal,#quickModal').removeClass('show'); overlay(false); });
        $(function(){ cartSummary(false); });
    })();
    </script>
</body>
</html>
