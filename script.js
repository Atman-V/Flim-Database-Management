const API_KEY = 'api_key=1cf50e6248dc270629e802686245c2c8';
const BASE_URL = 'https://api.themoviedb.org/3';
const API_URL = BASE_URL + '/discover/movie?sort_by=popularity.desc&'+API_KEY;
const IMG_URL = 'https://image.tmdb.org/t/p/w500';
const searchURL = BASE_URL + '/search/movie?'+API_KEY;

const genres = [
    {
      "id": 28,
      "name": "Action"
    },
    {
      "id": 12,
      "name": "Adventure"
    },
    {
      "id": 16,
      "name": "Animation"
    },
    {
      "id": 35,
      "name": "Comedy"
    },
    {
      "id": 80,
      "name": "Crime"
    },
    {
      "id": 99,
      "name": "Documentary"
    },
    {
      "id": 18,
      "name": "Drama"
    },
    {
      "id": 10751,
      "name": "Family"
    },
    {
      "id": 14,
      "name": "Fantasy"
    },
    {
      "id": 36,
      "name": "History"
    },
    {
      "id": 27,
      "name": "Horror"
    },
    {
      "id": 10402,
      "name": "Music"
    },
    {
      "id": 9648,
      "name": "Mystery"
    },
    {
      "id": 10749,
      "name": "Romance"
    },
    {
      "id": 878,
      "name": "Science Fiction"
    },
    {
      "id": 10770,
      "name": "TV Movie"
    },
    {
      "id": 53,
      "name": "Thriller"
    },
    {
      "id": 10752,
      "name": "War"
    },
    {
      "id": 37,
      "name": "Western"
    }
];

const main = document.getElementById('main');
const form =  document.getElementById('form');
const search = document.getElementById('search');
const tagsEl = document.getElementById('tags');
const prev = document.getElementById('prev');
const next = document.getElementById('next');
const current = document.getElementById('current');

var currentPage = 1;
var nextPage = 2;
var prevPage = 3;
var lastUrl = '';
var totalPages = 100;
var selectedGenre = [];

setGenre();

function setGenre() {
    tagsEl.innerHTML= '';
    genres.forEach(genre => {
        const t = document.createElement('div');
        t.classList.add('tag');
        t.id = genre.id;
        t.innerText = genre.name;
        t.addEventListener('click', () => {
            if(selectedGenre.length == 0){
                selectedGenre.push(genre.id);
            }else{
                if(selectedGenre.includes(genre.id)){
                    selectedGenre.forEach((id, idx) => {
                        if(id == genre.id){
                            selectedGenre.splice(idx, 1);
                        }
                    });
                }else{
                    selectedGenre.push(genre.id);
                }
            }
            getMovies(API_URL + '&with_genres=' + encodeURI(selectedGenre.join(',')));
            highlightSelection();
        });
        tagsEl.append(t);
    });
}

function highlightSelection() {
    const tags = document.querySelectorAll('.tag');
    tags.forEach(tag => {
        tag.classList.remove('highlight');
    });
    clearBtn();
    if(selectedGenre.length !=0){   
        selectedGenre.forEach(id => {
            const hightlightedTag = document.getElementById(id);
            hightlightedTag.classList.add('highlight');
        });
    }
}

function clearBtn(){
    let clearBtn = document.getElementById('clear');
    if(clearBtn){
        clearBtn.classList.add('highlight');
    }else{
        let clear = document.createElement('div');
        clear.classList.add('tag','highlight');
        clear.id = 'clear';
        clear.innerText = 'Clear x';
        clear.addEventListener('click', () => {
            selectedGenre = [];
            setGenre();            
            getMovies(API_URL);
        });
        tagsEl.append(clear);
    }
}

getMovies(API_URL);

function getMovies(url) {
    lastUrl = url;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if(data.results.length !== 0){
                showMovies(data.results);
                currentPage = data.page;
                nextPage = currentPage + 1;
                prevPage = currentPage - 1;
                totalPages = data.total_pages;
                current.innerText = currentPage;

                if(currentPage <= 1){
                  prev.classList.add('disabled');
                  next.classList.remove('disabled');
                }else if(currentPage >= totalPages){
                  prev.classList.remove('disabled');
                  next.classList.add('disabled');
                }else{
                  prev.classList.remove('disabled');
                  next.classList.remove('disabled');
                }
                tagsEl.scrollIntoView({behavior : 'smooth'});
            }else{
                main.innerHTML= `<h1 class="no-results">No Results Found</h1>`;
            }
        });
}

function showMovies(data) {
    main.innerHTML = '';
    data.forEach(movie => {
        const {title, poster_path, vote_average, overview, id} = movie;
        const movieEl = document.createElement('div');
        movieEl.classList.add('movie');
        movieEl.innerHTML = `
            <img src="${poster_path ? IMG_URL + poster_path : "http://via.placeholder.com/1080x1580"}" alt="${title}">
            <div class="movie-info">
                <h3>${title}</h3>
                <span class="${getColor(vote_average)}">${vote_average}</span>
            </div>
            <div class="overview">
                <h3>Overview</h3>
                ${overview}
                <br/> 
                <button class="know-more" id="${id}">Know More</button>
            </div>
        `;
        main.appendChild(movieEl);
        document.getElementById(id).addEventListener('click', () => {
            fetch(BASE_URL + '/movie/' + id + '?'+API_KEY)
            .then(res => res.json())
            .then(movieData => {
                fetch(BASE_URL + '/movie/' + id + '/credits?' + API_KEY)
                .then(res => res.json())
                .then(creditData => {
                    fetch(BASE_URL + '/movie/' + id + '/videos?' + API_KEY)
                    .then(res => res.json())
                    .then(videoData => {
                        showMovieDetails(movieData, creditData, videoData);
                    });
                });
            });
        });
    });
}

function showMovieDetails(movieData, creditData, videoData) {
    const {title, poster_path, vote_average, overview, genres, budget, revenue, release_date} = movieData;
    const {cast} = creditData;
    const {results} = videoData;
    const genresStr = genres.map(genre => genre.name).join(', ');
    const castStr = cast.map(actor => actor.name).join(', ');
    const youtubeLinks = results.filter(video => video.site === 'YouTube').map(video => `https://www.youtube.com/watch?v=${video.key}`).join(', ');

    const movieDetails = document.createElement('div');
    movieDetails.classList.add('movie-details');
    movieDetails.innerHTML = `
        <h1>${title}</h1>
        <div class="movie-info">
            <div>
                <strong>Genres:</strong> ${genresStr}
            </div>
            <div>
                <strong>Budget:</strong> ${budget}
            </div>
            <div>
                <strong>Revenue:</strong> ${revenue}
            </div>
            <div>
                <strong>Release Date:</strong> ${release_date}
            </div>
            <div>
                <strong>Cast:</strong> ${castStr}
            </div>
            <div>
                <strong>YouTube Links:</strong> ${youtubeLinks}
            </div>
        </div>
        <div class="overview">
            <h3>Overview</h3>
            ${overview}
        </div>
    `;
    main.innerHTML = '';
    main.appendChild(movieDetails);
}

function getColor(vote) {
    if(vote >= 8){
        return 'green';
    }else if(vote >= 5){
        return "orange";
    }else{
        return 'red';
    }
}

form.addEventListener('submit', (e) => {
    e.preventDefault();

    const searchTerm = search.value;
    selectedGenre = [];
    setGenre();
    if(searchTerm) {
        getMovies(searchURL+'&query='+searchTerm);
    }else{
        getMovies(API_URL);
    }

});

prev.addEventListener('click', () => {
    if(prevPage > 0){
        pageCall(prevPage);
    }
});

next.addEventListener('click', () => {
    if(nextPage <= totalPages){
        pageCall(nextPage);
    }
});

function pageCall(page){
    let urlSplit = lastUrl.split('?');
    let queryParams = urlSplit[1].split('&');
    let key = queryParams[queryParams.length - 1].split('=');
    if(key[0] != 'page'){
        let url = lastUrl + '&page=' + page;
        getMovies(url);
    }else{
        key[1] = page.toString();
        let a = key.join('=');
        queryParams[queryParams.length - 1] = a;
        let b = queryParams.join('&');
        let url = urlSplit[0] +'?'+ b;
        getMovies(url);
    }
}
