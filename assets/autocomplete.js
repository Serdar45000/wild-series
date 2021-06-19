document.getElementById('searchField').addEventListener('input', function (event) {
    // get value of searchField (<input id="serchField">) that has been put by the user
    let query = event.target.value;

    // get list (<ul id="autocomplete"></ul>) element to manipulate later
    let list = document.getElementById('autocomplete');

    // if query is not empty, do stuff
    if (query !== '') {
        // fetch data from ProgramController::autocomplete()
        fetch('/programs/autocomplete?q=' + query, { method: 'GET' })

            // get response body in json format
            .then(response => response.json())

            // manipulate data as we wish
            .then(programs => {
                // flush list at every request, so that suggestions don't pile up
                list.innerHTML = '';

                // forEach is a method in JS, which is weird, but you get used to it
                programs.forEach(program => {
                    // for each program, create a link that points to the program's details page
                    let link = document.createElement('a');
                    link.href = '/programs/' + program.id;
                    link.innerHTML = program.title;

                    // for each program, create a <li> element, and put the previously created link as child of it 
                    let li = document.createElement('li');
                    li.appendChild(link);

                    // add the <li> element at the end of the list
                    list.appendChild(li);
                });
            })
            ;
    } else {
        // if query is empty, flush the suggestions list
        list.innerHTML = '';
    }
});