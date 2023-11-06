<template>
    <div class="container">

        Add comment here
        <div>here is data => {{ data }}</div>
        <!-- <div>here is comments => {{ comments }}</div> -->
        <div class="return_button">
            <input type="button" onclick="history.go(-1);" value="Back">
        </div>
        <div class="table-responsive my-5">
            <Table :fields="fields" :comments="comments" @sendSelectedComment="selComment"></Table>
        </div>

        <!-- <div v-if="this.form.commented_comment_id">Selected comment: {{ this.getCommentContent }}
            <div><button type="button" @click="this.form.commented_comment_id = null" class="clear">Clear</button></div>
        </div> -->
        <!-- <div v-html="variable"></div> -->
        <div class="add-form">
            <form @submit.prevent="submit">
                <!-- #Region name -->
                <div class="form-group">
                    <input type="text" placeholder="Enter your name, please..." class="form-control" required
                        v-model="form.name" />

                </div>
                <div v-if="form.errors.name" v-text="form.errors.name" class="text-red-500 text-xs mt-1"></div>
                <!-- #Endregion -->

                <!-- #Region email -->
                <div class="form-group">
                    <input type="email" placeholder="Enter your email, please..." class="form-control" required
                        v-model="form.email" />
                </div>
                <div v-if="form.errors.email" v-text="form.errors.email" class="text-red-500 text-xs mt-1"></div>
                <!-- #Endregion -->

                <!-- #Region Url -->
                <div class="form-group">
                    <input type="url" placeholder="Enter your url http://" class="form-control" v-model="form.url"
                        pattern="https?://.*" />
                </div>
                <!-- #Endregion -->

                <!-- #Region Markdown buttons -->
                <div class="form-group ">
                    <div class="icons-container">
                        <img src="../icons/code-solid.svg" @click="handleImgClick($event)" name="code" class="icons" />
                        <img src="../icons/bold-solid.svg" class="icons" @click="handleImgClick($event)" name="bold" />
                        <img src="../icons/italic-solid.svg" class="icons" @click="handleImgClick($event)" name="italic" />
                        <img src="../icons/link-solid.svg" class="icons" @click="handleImgClick($event)" name="link" />
                    </div>
                </div>
                <!-- #Endregion -->

                <!-- #Region textarea -->
                <div class="form-group ">

                    <textarea v-model="form.content" name="content" class="form-control" required
                        placeholder="Enter content..." rows="4" cols="50" ></textarea>
                </div>
                <div v-if="form.errors.content" v-text="form.errors.content" class="text-red-500 text-xs mt-1"></div>
                <!-- #Endregion -->

                <!--#region Drop zone -->
                <div class="form-group">
                    <DropZone class="drop-area" @files-dropped="addFiles" #default="{ dropZoneActive }">
                        <label for="file-input">
                            <span v-if="dropZoneActive">
                                <span>Drop Them Here</span>
                                <span class="smaller">to add them</span>
                            </span>
                            <span v-else>
                                <span>Drag Your Files Here</span>
                                <span class="smaller">
                                    or <strong><em>click here</em></strong> to select (0-3) files (jpeg,gif,png,txt(100kb))
                                </span>

                            </span>

                            <input type="file" id="file-input" multiple @change="onInputChange" />
                        </label>
                        <ul class="image-list" v-show="files.length">
                            <FilePreview v-for="file of files" :key="file.id" :file="file" tag="li" @remove="removeFile" />

                        </ul>

                    </DropZone>

                </div>
                <div v-if="form.errors.files" v-text="form.errors.files" class="text-red-500 text-xs mt-1"></div>

                <!--#endregion End of Drop Zone -->

                <!-- #region Preview -->
                <div class="preview-container">

                    <span class="inner-preview-container">
                        <div class="comment-commented" v-if="this.form.commented_comment_id"
                            v-html="markdown.render(this.getCommentContent)"></div>
                        <button v-if="this.form.commented_comment_id" type="button"
                            @click="this.form.commented_comment_id = null" class="close-icon">&times;</button>
                    </span>

                    <!-- #Region Preview body -->

                    <div class='preview'>


                        <span v-html='markdown.render(form.content)'></span>
                        <ul class="image-list" v-show="files.length">
                            <FilePreview v-for="file of files" :key="file.id" :file="file" tag="li" @remove="removeFile" />

                        </ul>
                    </div>
                    <!-- #Endregion -->


                </div>
                <!-- #endregion -->

                <div>
                    <button type="submit" class="mybtn" :disabled="form.processing">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";

import Table from "../Components/Table.vue";
import "bootstrap/dist/css/bootstrap.min.css";
import DropZone from "../Components/AddForm/DropZone.vue"
import FilePreview from "@/Components/AddForm/FilePreview.vue";

import useFileList from '../utils/file-list'
import MarkdownIt from "markdown-it";
import MarkdownItHighlightjs from "markdown-it-highlightjs";

import DOMPurify from 'isomorphic-dompurify';
import debounce from "lodash/debounce"




export default {
    props: {
        data: {
            type: Object,
        },
        comments: {
            type: Array,
        },
    },
    components: {
        Table,
        DropZone,
        FilePreview,

    },
    data() {
        const markdown = new MarkdownIt().use(MarkdownItHighlightjs);
        const { files, addFiles, removeFile } = useFileList()
        const number = ref(Math.floor(Math.random() * 100000) + 1)


        // #region DOMPurify
        const config = {
            ALLOWED_TAGS: ['a', 'code', 'i', 'strong','p','em'],
            ALLOWED_ATTR: ['href', 'title'],
        };
        // #endregion


// #region Form
        const fields = ["name", "email", "content", "created_at", 'comment_id'];

        const form = useForm({
            email: `email${number.value}@mail.com`,
            name: `name${number.value}`,
            content: `content - ${number.value}`,
            url: null,
            commented_comment_id: null,
            level: this.data.level || 0,
            parent_id: this.data.parent_id,
            files: []

        })
        // #endregion

        return { fields, files, addFiles, removeFile, number, form, markdown, DOMPurify, config}
    },


    computed: {
        getCommentContent() {
            const item = this.comments.filter(item => item.comment_id === this.form.commented_comment_id)[0]
            return item.content
        }
    },
    methods: {
        submit() {

            if (this.files.length > 0) {
                this.form.files = this.files.map(file => file.file)
            }

            this.form.post('/add/comment')

        },
        selComment(id) {

            this.form.commented_comment_id = id
        },
        onInputChange(e) {
            this.addFiles(e.target.files)

            e.target.value = null // reset so that selecting the same file again will still cause it to fire this change
        },
        handleImgClick(e) {

            switch (e.target.name) {
                case "code":
                    this.form.content += " ```set some code here ```"
                    break;
                case "bold":
                    this.form.content += " **This is bold text**"
                    break;
                case "link":
                    this.form.content += '[link with title](http://example.com)'
                    break;
                case "italic":
                    this.form.content += "*This is italic text*"
                    break;
                default:
                    console.log("add.vue default switch")
                    break;
            }

        }

    },
    mounted() {
        console.log("Add component: Comments: ", this.comments);
    },
    watch:{
        'form.content': debounce( function (val){
            this.form.content = DOMPurify.sanitize(val,this.config)

        },2000),

    },

};
</script>

<!-- #Region  Styles-->

<style>
.container {
    text-align: center;
    margin: 50px auto;
}

.form-group {
    margin-top: 10px;
    display: flex;
    justify-content: space-evenly;
    flex-wrap: wrap;

}

.add-form {
    display: flex;
    flex-direction: column;
    justify-content: center;

}

.form-control {
    width: 80%;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;
}

/* #Region Preview block */

.preview-container {

    margin: auto;
    width: 80%;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;
    margin-top: 10px;
    padding: 1px;
}


.preview {

    word-wrap: break-word;
    text-align: start;
    padding: 5px;
    white-space-collapse: preserve;
    text-wrap: wrap;
    overflow-wrap: break-word;

}

.comment-commented {

    border-left: 2px solid #369;
    font-size: 15px;
    margin-top: 5px;
    margin-left: 5px;
    padding: 5px;
    text-align: start;


}

.comment-commented p {
    margin-bottom: 0;
}

/* .comment-commented:after {
    display: inline-block;
    content: "\00d7";
    background-color: #933;
    color: #fff;
    border-radius: 70%;
} */
.inner-preview-container {
    position: relative;
}

/* .comment-commented .close-icon {
    --size: 20px;

    line-height: var(--size);
    height: var(--size);
    border-radius: var(--size);
    box-shadow: 0 0 5px currentColor;
    right: 0.25rem;
    appearance: none;
    border: 0;
    padding: 0;
} */



.close-icon {
    --size: 20px;
    width: var(--size);
    border-radius: 70%;
    font-size: var(--size);
    background: #933;
    color: #fff;
    right: 0.25rem;
    top: 0;
    position: absolute;

}

/* #Endregion */

.mybtn {
    margin-top: 15px;
    width: 45%;
    background-color: #fff;
    border: 0.5px solid #369;
    border-radius: 10px;
    color: #000;
    padding: 8px;
    box-shadow: 0 0 10px 0 #369,
        0 0 10px 0 #369,
        0 0 5px 0 #369 inset;
}

.clear {
    border: 1px solid red;
    padding: 5px;
    width: 70px;
    margin: 5px;
    border-radius: 5px;
}

.clear:hover {
    background: red;
    color: white;
}

/* drop area styles */
.drop-area {
    width: 80%;
    /* max-width: 800px; */
    margin: 0 auto;
    padding: 50px;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;
    border-radius: 5px;
}

.drop-area[data-active=true] {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    background: rgba(255, 255, 255, 0.8);
}

label {
    font-size: 36px;
    cursor: pointer;
    display: block;
}

label span {
    display: block;
}

/* label input[type=file]:not(:focus-visible) { */
label input[type=file] {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

label .smaller {
    font-size: 16px;
}

.image-list {
    display: flex;
    list-style: none;
    flex-wrap: wrap;
    padding: 0;
}

.upload-button {
    display: block;
    appearance: none;
    border: 0;
    border-radius: 50px;
    padding: 0.75rem 3rem;
    margin: 1rem auto;
    font-size: 1.25rem;
    font-weight: bold;
    background: #369;
    color: #fff;
    text-transform: uppercase;
}


.return_button {
    text-align: left;
    color: blue;
    text-decoration: underline;
}

.icons {
    width: 30px;
    padding: 5px;
}

.icons-container {
    width: 80%;
    border-radius: 5px;
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;

}

/* end drop area styles */
</style>
<!-- #Endregion -->
