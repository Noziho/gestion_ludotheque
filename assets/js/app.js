import '../styles/app.scss';

let search = document.querySelector('#search');

search.addEventListener('submit', (e) => {
    e.preventDefault();
    let searchValue = document.querySelector('#searchValue');

    console.log(searchValue.value)

    searchValue.value = "";
})
