document.getElementById('searchField').addEventListener('input', function (event) {
    let query = event.target.value;
    let list = document.getElementById('autocomplete');
    if (query !== '') {
        fetch('/programs/autocomplete?q=' + query, { method: 'GET' })

            .then(response => response.json())
            .then(programs => {
                list.innerHTML = '';
                programs.forEach(program => {
                    let link = document.createElement('a');
                    link.href = '/programs/' + program.id;
                    link.innerHTML = program.title;
                    let li = document.createElement('li');
                    li.appendChild(link);

                    list.appendChild(li);
                });
            })
            ;
    } else {
        list.innerHTML = '';
    }
});