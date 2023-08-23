/**
 * Событие загрузки страницы
 */
document.addEventListener('DOMContentLoaded', () =>
{
    let modal = document.querySelector('#delete-post-modal-id');
    let form = modal.querySelector('form');

    /**
     * Событие открытия модального окна удаления поста
     */
    modal.addEventListener('show.bs.modal', (e) =>
    {
        let id =e.relatedTarget.getAttribute('data-bs-post-id');
        form.setAttribute('action', 'post-action.php?act=delete&id=' + id);
    });

    /**
     * Событие закрытия модального окна удаления поста
     */
    modal.addEventListener('hidden.bs.modal', () =>
    {
        form.setAttribute('action', '');
    });
});