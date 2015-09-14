$(function() {
    toastr.options.positionClass = 'toast-bottom-left';
    toastr.options.extendedTimeOut = 0; //1000;
    toastr.options.timeOut = 3000;
    toastr.options.fadeOut = 250;
    toastr.options.fadeIn = 250;
});
Phasty = {
    RGBChange: function() {
        $('#RGB').css('background', 'rgb(' + r.getValue() + ',' + g.getValue() + ',' + b.getValue() + ')');
    },

    /**
     * building tree of categories
     */
    categoriesTree: function(treeData, container) {
        this.buildTree = function(treeData) {
            var node = $('<div class="panel-group category-products" id="accordian"></div>');
            $.each(treeData, function(index, category) {
                if ((typeof category.children !== 'undefined') && (category.children.length > 0)) {
                    var sNode = $('<div class="panel panel-default"></div>');
                    var bNode = buildSubTree(category.children, category.title, sNode, 'accordian');
                    bNode.appendTo(node);
                } else if (typeof category.children === "undefined") {
                    var subNode = $('<div class="panel panel-default"></div>');
                    $('<div class="panel-heading"></div>')
                        .html('<h4 class="panel-title"><a href="/catalog/category/' + category.slug + '">' + category.title + '</a></h4>')
                        .appendTo(subNode);
                    subNode.appendTo(node);
                }
            });
            return node;
        };

        function buildSubTree(children, title, node, parent) {
            subCategory = children['0'];
            if ((typeof subCategory === 'object') && (typeof subCategory.children !== 'undefined') && (subCategory.children.length > 0)) {
                $('<div class="panel-heading"></div>')
                    .html('<h4 class="panel-title"><a data-toggle="collapse" data-parent="#' + parent + '" href="#' + subCategory.slug + '"><span class="badge pull-right"><i class="fa fa-plus"></i></span>' + title + '</a></h4>')
                    .prependTo(node);
                var sNode = $('<div id="' + subCategory.slug + '" class="panel-collapse collapse"></div>');
                var body = $('<div class="panel-body">');
                var subNode = buildSubTree(subCategory.children, subCategory.title, body, subCategory.slug);
                subNode.appendTo(sNode);
                sNode.appendTo(node);
            } else {
                $('<div class="panel-heading"></div>')
                    .html('<h4 class="panel-title"><a data-toggle="collapse" data-parent="#' + parent + '" href="#' + subCategory.slug + '"><span class="badge pull-right"><i class="fa fa-plus"></i></span>' + title + '</a></h4>')
                    .prependTo(node);
                $('<div id="' + subCategory.slug + '" class="panel-collapse collapse">')
                    .html('<div class="panel-body"><ul><li><a href="/catalog/category/' + subCategory.slug + '">' + subCategory.title + ' </a></li></ul></div>')
                    .appendTo(node);
            }
            return node;
        }
        this.tree = this.buildTree(treeData);

        var treeCon = container;

        treeCon.append(this.tree);
    },

    searchProducts: function(page) {
        $("#productsList").css('opacity', '0.3');
        var send = Phasty.getSearchData();
        send = send + '&page=' + (page || 1);
        $.ajax('/catalog/search/', {
            method: 'POST',
            data: send,
            success: Phasty.ShowSearchResult,
            complete: [Phasty.RequestComplete, Phasty.ShowToast('info', 'page with products updated')]
        });
    },

    getSearchData: function() {
        return $('.searchProductsBlock :input').not(':button, :reset').filter(function() {
            return ($(this).val() != "0" && $(this).val() !== "");
        }).serialize();
    },

    RequestComplete: function(data) {
        $("#productsList").css('opacity', '1');
    },
    ShowSearchResult: function(data) {
        $("#productsList").empty();
        if (data !== '') {
            $("#productsList").html(data);
        } else
            $("#productsList").html('Nothing found with such search criteria');
    },
    ShowToast: function(type, msg) {
        toastr[type](msg);
    }
};

$(document).ready(function() {
    if ($('#priceRangeSlider').length) {
        $('#priceRangeSlider').noUiSlider({
            start: [$('#priceRangeSlider').data('slider-min') || 0, $('#priceRangeSlider').data('slider-max') || 0],
            connect: true,
            range: {
                'min': [$('#priceRangeSlider').data('slider-value')[0] || 0],
                'max': [$('#priceRangeSlider').data('slider-value')[1] || 0]
            }
        });

        $("#priceRangeSlider").Link('lower').to($('#priceRangeLeftValue'));
        $("#priceRangeSlider").Link('upper').to($('#priceRangeRightValue'));
        $('#brands').select2();
        $('.attributeValues').select2();
        $('body').delegate('#searchProductsButton', 'click', function() {
            return Phasty.searchProducts(1);
        });
        $('body').delegate('.ajaxPager', 'click', function() {
            var page = $(this).data('page-number');
            return Phasty.searchProducts(page);
        });
    }
    $('body').delegate('.compare_wish_list', 'click', function() {
        var $this = $(this);
        if ($this.data('action') == 'remove') {
            $(this).closest('.col-sm-3').remove();
        }
        $.ajax('/compare/addOrRemove', {
            method: 'POST',
            data: {
                'productId': $this.data('product-id'),
                'what': $this.data('what-list'),
                'action': $this.data('action')
            },
            complete: Phasty.ShowToast('info', 'product ' + $this.data('action'))
        });
    });
    $('body').delegate('.add-to-cart', 'click', function() {
        var $this = $(this);
        var productId = $this.data('product-id');
        var quantity = $('#product_id_' + productId).val();
        $.ajax('/cart/add', {
            method: 'POST',
            data: {
                'productId': productId,
                'quantity': quantity
            },
            complete: Phasty.ShowToast('success', 'product added to cart')
        });
    });
    $('body').delegate('.cart_delete_product', 'click', function() {
        $.ajax('/cart/remove', {
            method: 'POST',
            data: {
                'productId': $(this).data('product-id')
            },
            success: Phasty.ShowSearchResult,
            complete: [Phasty.RequestComplete, Phasty.ShowToast('info', 'product deleted from cart')]
        });
    });
    $("body").delegate('input.cart_quantity_input', 'change', function() {
        var $this = $(this);

        function sendAjax() {
            $.ajax('/cart/quantity', {
                method: 'POST',
                data: {
                    'quantity': $this.val(),
                    'productId': $this.data('product-id')
                },
                success: Phasty.ShowSearchResult,
                complete: [Phasty.RequestComplete, Phasty.ShowToast('success', 'quantity changed')]
            });
        }

        window.setTimeout(sendAjax, 1000);
    });
    $('body').delegate('#review_button', 'click', function() {
        var $this = $(this);
        var content = $('textarea').val();
        var productId = $this.data('product-id');
        if (content) {
            $.ajax('/reviews/add', {
                method: 'POST',
                data: {
                    'productId': productId,
                    'content': content
                },
                complete: [$(this).closest('#review_block').fadeOut(2000), Phasty.ShowToast('success', 'Your comment will be seen after moderation')]
            });
        } else {
            Phasty.ShowToast('info', 'please write something first');
        }
    });
    $("#jRateReadOnly").jRate({
        rating: $("#jRateReadOnly").data('rate-number'),
        readOnly: true
    });
    $("#jRate").jRate({
        rating: 3
    });
    return new Phasty.categoriesTree($.parseJSON($('#categories-tree-source').text()), $('#categories-container'));
});
