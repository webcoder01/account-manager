require('./../css/account.scss');

const $formTabs = document.querySelectorAll('#op-form-action a');
const $listTabs = document.querySelectorAll('#op-list-action a');
const $budgetTabs = document.querySelectorAll('#op-budget-action a');
handleTabs($formTabs, true);
handleTabs($listTabs, false);
handleTabs($budgetTabs);

function handleTabs($tabs, isForm)
{
    if('undefined' === typeof isForm) {
        isForm = null;
    }
    
    for(let i = 0; i < $tabs.length; i++)
    {
        $tabs[i].addEventListener('click', () => {
            const idTarget = $tabs[i].getAttribute('data-target');
            const $target = document.querySelector(idTarget);
            hideTabTargets(isForm);
            $tabs[i].parentNode.classList.add('is-active');
            $target.classList.add('is-active');
        });
    }
}

function hideTabTargets(isForm)
{
    const targetType = true === isForm ? 'form' : (null === isForm ? 'budget' : 'list');
    const $tabs = document.querySelectorAll('#op-' + targetType + '-action li');
    const $targets = document.getElementsByClassName(targetType + '-target');
    
    for(let i = 0; i < $tabs.length; i++) {
        $tabs[i].classList.remove('is-active');
    }
    
    for(let i = 0; i < $targets.length; i++) {
        $targets[i].classList.remove('is-active');
    }
}