const $controls = document.getElementsByClassName('control');

for(let i = 0; i < $controls.length; i++)
{
    if(Node.ELEMENT_NODE === $controls[i].nodeType)
    {
        $controls[i].addEventListener('click', () => {
            const $flag = $controls[i].querySelector('.control-flag');
            if(null !== $flag) {
                $flag.classList.toggle('selected');
            }
        });
    }
}