const $login = document.querySelector('.login-input');
if(null !== $login)
{
    $login.addEventListener('keydown', event => {
        event.target.value = event.target.value.toLowerCase();
    });
}