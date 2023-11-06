
<template>
    <div class="table-responsive-sm table-rows">
        <table id="tableComponent" class="table table-striped styled-table table-hover table-sm caption-top">
            <caption>List of comments</caption>
            <thead>
                <tr>
                    <th v-for="field in fields" :key="field" @click="sortTable(field)" v-show="field !== 'comment_id'">
                        {{ field }}
                        <i :class="{
                            'bi bi-sort-alpha-down': sort,
                            'bi bi-sort-alpha-up': !sort,
                        }" aria-label="Sort Icon"></i>
                    </th>
                </tr>
            </thead>
            <tbody class="">
                <tr v-for="item, index in sortedList" :key="`${item}+${index}`">
                    <td v-for="field in fields" :key="`${field}+${index}`" v-show="field !== 'comment_id'" class="table-cell">
                        <span
                            v-html='field == "created_at" ? format_date(item[field]) : (field == "content" ? markdown.render(item[field]) : item[field])'></span>
                    </td>
                    <td><button class="selected" @click="$emit('sendSelectedComment', item.comment_id)">Select</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import moment from "moment";
import { ref, computed } from "vue";
import "bootstrap-icons/font/bootstrap-icons.css";
import MarkdownIt from "markdown-it";
import MarkdownItHighlightjs from "markdown-it-highlightjs";


const props = defineProps({
    comments: Array,
    fields: Array,
});

const markdown = new MarkdownIt().use(MarkdownItHighlightjs);

const dynamicSort = (property) => {
    var sortOrder = 1;
    if (property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a, b) {

        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}


let sort = ref(false);
let updatedList = ref([]);
const emits = defineEmits(['sendSelectedComment'])


const sortTable = (col) => {
    sort.value = !sort.value;

    if (sort.value) {
        updatedList.value = (props.comments.sort(dynamicSort(col)));
    } else {
        updatedList.value = (props.comments.sort(dynamicSort(`-${col}`)));
    }

};



const sortedList = computed(() => {
    if (sort.value) {
        return updatedList.value;
    }
    return props.comments;
});


const format_date = (value) => {
    if (value) {
        return moment(String(value)).format("DD.MM.YYYY в HH:mm");
    }
}



</script>

<style>
.selected {
    border: 1px solid rgb(133 204 145);
    padding: 3px;
    width: 70px;
    margin: 0;
    border-radius: 5px;
}

.selected:hover {
    background: rgb(133 204 145);
    color: white;
}

.styled-table {

    border-collapse: collapse;
    margin: 25px 0;
    font-size: 1em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-rows {
    max-height: 400px;
    overflow-y: auto;
}
.table-cell{
    vertical-align: baseline;
}
</style>
