import $ from 'jquery';

export const order = () => {
    const categorySelect = $('.order-category');
    const orderItemAddButton = $('.add-order-item');

    const addFormDeleteLink = (item) => {
        const removeFormButton = $('<button>')
            .text('Remove product')
            .addClass('btn btn-danger col-6');

        item.append(removeFormButton);

        removeFormButton.on('click', (event) => {
            event.preventDefault();
            item.remove();
        });
    }

    const addFormToCollection = (event) => {
        const {collectionHolderClass} = event.currentTarget.dataset;
        const collectionHolder = $(`.${collectionHolderClass}`);
        const item = $('<li>');

        item.html(
            collectionHolder
                .data('prototype')
                .replace(/__name__/g, collectionHolder.data('index'))
        );

        collectionHolder.append(item);
        collectionHolder.data('index', collectionHolder.data('index') + 1);

        addFormDeleteLink(item);
    };

    orderItemAddButton.each((i, button) => {
        $(button).on('click', addFormToCollection);
    });

    categorySelect.on('change', (event) => {
        $.ajax({
            url: categorySelect.data('products-url'),
            data: {
                categoryId: categorySelect.val()
            },
            success: (html) => {
                const productSelect = $('.order-item-product');

                if (!html) {
                    productSelect
                        .remove()
                        .addClass('d-none');
                    return;
                }

                productSelect
                    .html(html)
                    .removeClass('d-none');

                $('.order-items').data('prototype', html);
            }
        });
    });
};
