Vue.component('story', {
    template: '#story-template',
    props: ['story'],
    methods: {
        upvoteStory: function (story) {
            story.upvotes++;
            axios.patch('/api/stories/' + story.id, story)
            .then(function (data) {
                console.log(data);
            });
        },
        editStory: function (story) {
            story.editing = true;
        },
        updateStory: function (story) {
            axios.patch('/api/stories/' + story.id, story)
            .then(function (data) {
                console.log(data);
            });
            story.editing = false;
        },
        storeStory: function (story) {
            axios.post('/api/stories/', story)
            .then(function (response) {
                console.log(response);
                Vue.set(story, 'id', response.data.id);
                story.editing = false;
            });
        },
        deleteStory: function (story) {
            var index = vm.stories.indexOf(story);
            this.$parent.stories.splice(index, 1);
            //vm.stories.splice(index, 1); - то саме що і вище
            axios.delete('/api/stories/' + story.id, story)
                .then(function (data) {
                    console.log(data);
                });
        }

    }
});

var vm = new Vue({
    el: '.container',
    data: {
        stories: [],
        pagination: []
    },
    methods: {
        createStory: function () {
            var newStory = {
                plot: '',
                upvotes: 0,
                editing: true
            };
            this.stories.push(newStory);
        },
        fetchStories: function (page_url) {
            var vm = this;
            page_url = page_url || '/api/stories';
            axios.get(page_url)
            .then(function (response) {
                var storiesReady = response.data.data.map(function (story) {
                    story.editing = false;
                    return story;
                });

                vm.makePagination(response.data);
                vm.stories = storiesReady;
                //Vue.set(vm, 'stories', storiesReady);- то саме що і вище
            });
        },
        makePagination: function (data) {
            var pagination = {
                current_page: data.current_page,
                last_page: data.last_page,
                next_page_url: data.next_page_url,
                prev_page_url: data.prev_page_url
            };
            Vue.set(vm, 'pagination', pagination);
        }
    },
    mounted: function () {
        this.fetchStories();
    }

});