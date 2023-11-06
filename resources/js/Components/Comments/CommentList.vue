<template>
    <div>
      <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" />
    </div>
     <div class="btn_container_main">
            <button @click="goToForm()" class="mybtn">
                Comment
            </button>
        </div>
  </template>

  <script>
  import CommentItem from '../Comments/CommentItem.vue';
  import { router } from "@inertiajs/vue3";

  export default {
    props: {
      comments: Object, // Pass the array of comments as a prop
    },
    components: {
      CommentItem,
    },
    methods:{
        goToForm() {
            console.log("Clicked")
            router.visit(`/show/form` );
        },
    }
  };
  </script>

<style>

.btn_container{
    display: flex;
    justify-content: flex-start;
}

.btn_container_main{
    display: flex;
    justify-content: flex-end;
}

.mybtn{

    width: 150px;
}
</style>

