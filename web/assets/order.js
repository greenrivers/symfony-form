import $ from 'jquery';

export const order = () => {
    const categorySelect = $('.order-category');
    const orderItemAddButton = $('.add-order-item');
    const orderItemRemoveButton = $('.remove-order-item');

    const addFormDeleteLink = (item) => {
        const buttonText = item.closest('ul').data('remove-order-item');
        const removeFormButton = $('<button>')
            .text(buttonText)
            .addClass('btn btn-danger col-6');

        item.append(removeFormButton);

        removeFormButton.on('click', removeFormFromCollection);
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

        const index = collectionHolder.data('index');
        collectionHolder.append(item);
        collectionHolder.data('index', index + 1);

        const productSelect = item.find('select');
        const quantityInput = item.find('input');
        productSelect.prop('name', `order[orderItems][${index}][product]`);
        quantityInput.prop('name', `order[orderItems][${index}][quantity]`);

        addFormDeleteLink(item);
    };

    const removeFormFromCollection = (event) => {
        const button = event.currentTarget;
        const item = button.closest('li');

        event.preventDefault();
        item.remove();
    }

    orderItemAddButton.on('click', addFormToCollection);
    orderItemRemoveButton.on('click', removeFormFromCollection);

    categorySelect.on('change', (event) => {
        $.ajax({
            url: categorySelect.data('products-url'),
            data: {
                categoryId: categorySelect.val()
            },
            success: (html) => {
                const orderItems = $('.order-items');
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

                orderItems.data('prototype', html);
            }
        });
    });
};
