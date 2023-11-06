<template>
    <div class="comment-app">
        <h1>Comments</h1>
        <CommentList :comments="comments.data" />
    </div>
    <!-- Add pagination links here -->
    <div>
        <Paginator :totalPages="comments.total" :perPage="comments.per_page" :currentPage="comments.current_page"
            @pagechanged="onPageChange" :from="1" :to="comments.last_page" />
    </div>
</template>

<script>
import CommentList from "../Comments/CommentList.vue";
import Paginator from "../Paginator.vue";
import { router } from "@inertiajs/vue3";

export default {
    props: {
        comments: Object, // Pass the comment data as a prop
    },
    components: {
        CommentList,
        Paginator,
    },
    methods: {
        onPageChange(page) {
            this.currentPage = page;
            router.visit(`/?page=${page}`);
        },
    },
    mounted() {
        console.log("Comments: ", this.comments);
    },
};
</script>

<style>
.comment-app {
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    border-radius: 5px;
    transition: 0.2s ease;
    padding: 10px;
    margin:10px;

}

h1 {
    font-size: 24px;
    margin-bottom: 10px;
}
</style>
