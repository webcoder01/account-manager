const $showActions = document.getElementsByClassName('action-modal');
const $hideActions = document.getElementsByClassName('modal-close');

for(let i = 0; i < $showActions.length; i++)
{
    if($showActions[i].nodeType === Node.ELEMENT_NODE)
    {
        $showActions[i].addEventListener('click', event => {
            event.preventDefault();
            displayModal($showActions[i].getAttribute('data-modal'));
        });
    }
}

for(let i = 0; i < $hideActions.length; i++)
{
    if($hideActions[i].nodeType === Node.ELEMENT_NODE)
    {
        $hideActions[i].addEventListener('click', event => {
            let $modal = $hideActions[i].parentElement.parentElement;
            if($hideActions[i].classList.contains('btn'))
            {
                event.preventDefault();
                $modal = $hideActions[i].closest('.modal');
            }

            if($modal.classList.contains('modal'))
            {
                hideModal($modal.getAttribute('id'));
            }
        });
    }
}

const displayModal = (idModal) => {
    const $modal = document.getElementById(idModal);
    const $body = document.getElementsByTagName('body');

    if(null !== $modal)
    {
        $body[0].classList.add('modal-active');
        $modal.parentElement.style.display = 'block';
        $modal.style.display = 'block';
    }
};

const hideModal = (idModal) => {
    const $modal = document.getElementById(idModal);
    const $body = document.getElementsByTagName('body');

    if(null !== $modal)
    {
        $modal.style.display = 'none';
        $modal.parentElement.style.display = 'none';
        $body[0].classList.add('modal-active');
    }
};