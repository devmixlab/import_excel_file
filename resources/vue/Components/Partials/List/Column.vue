<script setup>
// import SearchInput from '@/Components/Blog/Search/Input.vue';
// import AuthenticatedLayout from '@/Layouts/Authenticated/AuthenticatedLayout.vue';
// import { Head, Link } from '@inertiajs/vue3';
// import Pagination from '@/Components/Pagination.vue';
// import SearchFoundAlert from '@/Components/Blog/Search/FoundAlert.vue';
// import ShownItemsOnPage from '@/Components/Blog/ShownItemsOnPage.vue';
// // import TopAction from './TopAction.vue';
// import { usePage, router } from '@inertiajs/vue3'

import { reactive, computed } from 'vue'

//
// import Helper from '@/Compositions/Helper.js'
//
// const page = usePage()
// const {formatDate, firstLetterToUppercase, getUrlQueryParamsAsObj} = Helper();
//
// const urlQuery = getUrlQueryParamsAsObj();
//
const props = defineProps([
  'data','name'
])

const column = computed(() => {
  if(!props.data)
    return null;

  return props.data[props.name];
})
//
// // console.log(props.componentDropdown)
//
// // const search = getUrlQueryParamsAsObj(['search'], true);
//
// const data = computed(() => {
//   return props.paginator.data;
// })
//
// // let data = props.paginator.data;
// const links = props.paginator.links;
// const current_page = props.paginator.current_page;
// const per_page = props.paginator.per_page;
//
// const firstRowOnThisPage = current_page * per_page - per_page + 1;
//
// function confirmDelete() {
//   if(!confirm("Are you sure?"))
//     return false;
//
//   // page.props.flash.success = false;
// }

// console.log(data)

// function onSuccessDeleteAll() {
//     // data.splice(0, data.length);
//     router.visit(route('dashboard.post.index', {
//         preserveState: true,
//     }));
// }

// function onSuccessDelete(id) {
//   return (response) => {
//     // console.log(props.paginator.data.length)
//
//
//     // if(props.paginator.prev_page_url === null)
//     //     return;
//     //
//     // if(props.paginator.data.length === 0)
//     //     router.visit(props.paginator.prev_page_url, {
//     //         // preserveState: true,
//     //         // replace: true,
//     //     });
//
//
//     // console.log(id);
//     // console.log(response);
//     // let row = data.find(obj => {
//     //     return obj.id === id;
//     // })
//     // const index = data.findIndex(obj => obj.id === id);
//     // data.splice(index, 1);
//     // if(data.length === 0 && props.paginator.prev_page_url){
//     //     router.visit(props.paginator.prev_page_url);
//     // }
//
//     // console.log(index)
//   }
//   // return confirm("Are you sure?");
// }

</script>

<template>
  <td>
    <template v-if="column">
      {{column}}
    </template>
    <slot v-else></slot>
  </td>
</template>