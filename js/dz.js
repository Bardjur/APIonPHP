Vue.component('movies', {
    template: '#c-movies',
    props:['movie'],
    methods: {
        editMovie: function (movie) {
            movie.isEdit = true;
        },
        saveEdit: function (movie) {
            axios.patch('http://apivue/api/movies/' + movie.id, movie)
            .then(function (response) {
                movie.isEdit = false;
                console.log(response);
            });
        },
        saveMovie: function (movie) {
            axios.post('http://apivue/api/movies/', movie)
            .then(function (response) {
                Vue.set(movie, 'id', response.data.id);
                movie.isEdit = false;
                console.log(response);
            });
        },
        deleteMovie: function (movie) {
            //var index = this.$parent.movies.indexOf(movie); замість vm.$data.movies.length - 1
            vm.$data.movies.splice(vm.$data.movies.length - 1, 1);
            axios.delete('http://apivue/api/movies/' + movie.id)
                .then(function (response) {
                    console.log(response); 
            });
        },
    }
});

vm = new Vue({
    el: '.container',
    data: {
        movies: []
    },
    methods:{
        getMovies: function (){
            axios.get('http://apivue/api/movies/')
            .then(function(response){
                var data = response.data.map(function(item){
                    item.isEdit = false;
                    return item;
                });
                vm.movies = data;
            });
        },
        addMovie: function () {
            Vue.set(this.movies, this.movies.length, {
                title: '',
                director: '',
                isEdit: true
            }); // або this.movies.push({...});
            console.log(this.movies.length);
        }
    },
    mounted: function () {
        this.getMovies();
    }
});