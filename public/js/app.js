function confirmEmptyCart(){

    if (confirm("¿Seguro que desea vaciar el carrito? "))
        return true;
    else
        return false;
}

function confirmDeleteItem(){

    if (confirm("¿Seguro que desea eliminar este producto? "))
        return true;
    else
        return false;
}

(function(){
    var infoCarro = $("#infoCarroProducto");
    var mask = $("#mask");
    var cuantosEl = $(".nav.navbar-nav.navbar-right .badge");
    
    var cartElements = {
        total: infoCarro.find("#data-container .total"), 
        title: infoCarro.find("#data-container .title"), 
        desc: infoCarro.find("#data-container .desc"),
        img: infoCarro.find("#data-container img"),
        cantidad: infoCarro.find("#data-container #cantidad"),
        updateButton: infoCarro.find("#data-container .update")
    };    

    $( ".thumbnail .btn.btn-danger, #ficha-producto .btn.btn-danger" ).click(function(event) {
        event.preventDefault();
        mask.show();
        var el =  $(event.target);
        var href = el.prop('href');
        var parts = href.split("/");
        var id = parts[5];
        var hrefJson = "/cart/add/json/" + id; 
        var jqxhr = $.get( hrefJson, function(data) {
            setTimeout(function(){
                var jData = JSON.parse(data);               
                cuantosEl.html(jData.totalItems);
                cartElements.total.html(jData.totalCarro);
                cartElements.title.html(jData.producto.nombre);
                cartElements.desc.html(jData.producto.descripcion);
                cartElements.img.attr("src", jData.rutaImagen + jData.producto.foto);
                cartElements.cantidad.val(jData.itemCount);
                mask.hide();
                infoCarro.modal();

                cartElements.updateButton.unbind();
                cartElements.updateButton.click(function(event){
                    event.preventDefault();
                    var hrefUpdate = "/cart/update/json/" + id; 
                    var hrefUpdateQuantity = hrefUpdate + "/" + cartElements.cantidad.val();
                    var actualizar = $.post( hrefUpdateQuantity, {}, function(data) {
                        jData = JSON.parse(data);
                        cartElements.total.html(jData.totalCarro);
                        cuantosEl.html(jData.cuantos);
                    });
                });
            }, 500);
          })
          .fail(function() {
            alert( "error" );
            mask.hide();
          });
    });
})();