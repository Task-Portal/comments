<template>
    <div class="comment-item">

        <!-- #Region First line -->
        <div class="first-line">
            <img :src="'https://via.placeholder.com/50x50'" alt="" class="avatar" />

            <b>{{ comment.user_name }}</b>
            <span>{{ format_date(comment.created_at) }}</span>
        </div>
        <!-- #Endregion -->

        <!-- #Region Commented Comment -->
        <div class="commented comment-content" v-if="comment.commented_comment_content"
            v-html="markdown.render(comment.commented_comment_content)"></div>
        <!-- #Endregion -->

        <!-- #Region comment body -->
        <div class="btn_container">
            <div class="comment-content" v-html="markdown.render(comment.content)"></div>

            <ul class="flex flex-row gap-5">
                <li v-for="file in comment.files" :key="file.id">

                    <a v-if="file.file_type.startsWith('image/')" :href="getFileUrl(file)" target="_blank">
                        <img :src="getFileUrl(file)" :alt="file.original_name"
                            class="flex-none rounded-md bg-slate-100 image-decorator" width="80">
                        {{ file.original_name.length > 15 ? file.original_name.substring(0, 15) + "..." : file.original_name
                        }}</a>

                    <a v-else :href="getFileUrl(file)" :download="file.original_name">
                        <img :src="`${this.app_url}/storage/icons/document.jpg`" :alt="file.original_name"
                            class="flex-none rounded-md bg-slate-100" width="80">
                        {{ file.original_name.length > 15 ? file.original_name.substring(0, 15) + "..." : file.original_name }}
                    </a>

                </li>
            </ul>
            <div style="position: absolute; bottom: 0;  right: 0;">
                <button @click="goToAddCommentForm(comment.level, comment.id)" class="mybtn comment_btn">
                    Comment
                </button>
            </div>
            <!-- <div><code>Some code will be here</code></div> -->
        </div>
        <!-- #Endregion -->



        <div v-if="comment.children && comment.children.length">
            <CommentItem v-for="comment in comment.children" :key="comment.id" :comment="comment" />
        </div>

    </div>
</template>

<script>
import moment from "moment";
import { router } from "@inertiajs/vue3";
import MarkdownIt from "markdown-it";
import MarkdownItHighlightjs from "markdown-it-highlightjs";



export default {
    name: "CommentItem",
    props: {
        comment: Object,
    },
    data() {
        const markdown = new MarkdownIt().use(MarkdownItHighlightjs);
        const app_url = import.meta.env.VITE_APP_URL
        return { upHere: false, app_url, markdown }
    },
    methods: {
        format_date(value) {
            if (value) {
                return moment(String(value)).format("DD.MM.YYYY в HH:mm");
            }
        },
        goToAddCommentForm(level, parent_id) {
            console.log("parent_id: ", parent_id);
            router.visit(`/show/form`, {
                data: {
                    level: level + 1,
                    parent_id: parent_id,
                },
            });
        }, getFileUrl(file) {


            if (file.file_type.startsWith('image/')) {

                return `${this.app_url}/storage/images/${file.unique_name}`
            } else {
                return `${this.app_url}/storage/text/${file.unique_name}`

            }

        }


    },
};
</script>

<style>
.image-decorator {
    -webkit-filter: grayscale(100%);
    filter: grayscale(100%);
    -webkit-transition: .3s ease-in-out;
    transition: .3s ease-in-out;
}


.image-decorator:hover {
    position: relative;
    z-index: 10;
    border: 1px solid red;
    transform: scale(5);
    object-fit: cover;

    -webkit-filter: grayscale(0);
    filter: grayscale(0);

    transition-duration: 1000ms;


}

.comment-item {
    /* border: 1px solid #ccc; */
    padding: 5px;
    margin: 10px;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;
    border-radius: 5px;
}

p {
    margin: 5px 0;
}

.avatar {
    border-radius: 50%;
}

.first-line {
    display: flex;
    align-items: center;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px;
    background-color: #dee2e296;
}

.commented {
    border-left: 2px solid #369;
    padding-left: 3px;
    font-size: 15px;
}

.comment-content {
    margin-top: 5px;
    padding: 5px;
}

.mybtn {
    background-color: #fff;
    border: 0.5px solid #369;
    border-radius: 10px;
    color: #000;
    padding: 8px;
    box-shadow: 0 0 10px 0 #369, 0 0 10px 0 #369, 0 0 5px 0 #369 inset;
}

/* .custom-image {
    width: 60px;
    height: 88px;
} */

.btn_container {
    display: inline-block;
    position: relative;
}

.comment_btn {
    /* display: table-cell; */
    display: none;
    color: black;
    background-color: white;
    width: 100px;
    height: 33px;
    font-size: 12px;
    vertical-align: middle;
}


.btn_container:hover .comment_btn {
    display: inline-block;
    /* position: absolute; */
    left: 0;
    right: 0;
    bottom: 20px;
    width: 100%;
    text-align: center;

}



</style>
