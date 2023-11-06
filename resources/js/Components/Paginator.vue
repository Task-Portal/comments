<template>
    <div class="pagination-container">

    <ul class="pagination">

        <li class="pagination-item">
            <button
                type="button"
                @click="onClickFirstPage"
                :disabled="isInFirstPage"
            >
                First
            </button>
        </li>

        <li class="pagination-item">
            <button
                type="button"
                @click="onClickPreviousPage"
                :disabled="isInFirstPage"
            >
                Previous
            </button>
        </li>

        <!-- Visible Buttons Start -->

        <li v-for="page in pages" :key="page.name" class="pagination-item">
            <button
                type="button"
                :disabled="page.isDisabled"
                @click="onClickPage(page.name)"
                :class="{ active: isPageActive(page.name) }"
            >
                {{ page.name }}
                <!-- <Link :href='/?page=1'>{{ page.name }}</Link> -->
            </button>
        </li>

        <!-- Visible Buttons End -->

        <li class="pagination-item">
            <button
                type="button"
                @click="onClickNextPage"
                :disabled="isInLastPage"
            >
                Next
            </button>
        </li>

        <li class="pagination-item">
            <button
                type="button"
                @click="onClickLastPage"
                :disabled="isInLastPage"
            >
                Last
            </button>
        </li>
    </ul>
</div>
</template>

<script>
import { Link } from '@inertiajs/vue3'


export default {
    props: {
        maxVisibleButtons: {
            type: Number,
            required: false,
            default: 1,
        },
        totalPages: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            required: true,
        },
        currentPage: {
            type: Number,
            required: true,
        },
        from: {
            type: Number,
            required: true,
        },
        to: {
            type: Number,
            required: true,
        },
    },
    computed: {
        startPage() {
            // When on the first page
            // if (this.currentPage === 1) {
            //     return 1;
            // }

            // // When on the last page
            // if (this.currentPage === this.totalPages) {
            //     return this.totalPages - this.maxVisibleButtons;
            // }

            // // When inbetween
            // return this.currentPage - 1;
        },
        pages() {
            const range = [];

            for (let i = this.from; i <= this.to; i++) {
                range.push({ name: i, isDisabled: i === this.currentPage });
            }
            console.log("Range: ",range)
            return range;
        },
        isInFirstPage() {
            return this.currentPage === 1;
        },
        isInLastPage() {
            return this.currentPage === this.to;
        },
    },
    methods: {
        onClickFirstPage() {
            this.$emit("pagechanged", 1);
        },
        onClickPreviousPage() {
            this.$emit("pagechanged", this.currentPage - 1);
        },
        onClickPage(page) {
            this.$emit("pagechanged", page);
        },
        onClickNextPage() {
            this.$emit("pagechanged", this.currentPage + 1);
        },
        onClickLastPage() {
            this.$emit("pagechanged", this.to);
        },
        isPageActive(page) {
            return this.currentPage === page;
        },
    },
};
</script>

<style>
.pagination-container{


    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.333);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transition: 0.2s ease;
    border-radius: 50%;
    padding: 1px;
    gap: 5px;

}



.pagination {
   
    display: flex;
    gap: 5px;
    flex-direction: row;
    align-items: center;
    margin: 0;
}


.pagination-item {
    display: inline-block;
}

.active {
    /* background-color: #fff; */
    border: 0.5px solid #369;
    border-radius: 10px;
    color: #000;
    padding: 8px;
    box-shadow: 0 0 10px 0 #369,
        0 0 10px 0 #369,
        0 0 5px 0 #369 inset;
}
button {
    margin: 3px;
    cursor: pointer;
}
</style>
