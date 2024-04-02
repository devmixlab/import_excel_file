<script setup>
// import { useForm } from '@inertiajs/vue3';
import Helper from '@/Compositions/Helper.js'

// const {getUrlQueryParamsAsObj} = Helper();
const urlQuery = getUrlQueryParamsAsObj(['search','tag','top','with_post'], true);

const props = defineProps({
  route: {
    type: String,
    required: true,
  },
});

// const form = useForm({
//   search: urlQuery.search
// });

const submit = () => {
  // console.log(555)
  // form.get(route(props.route, Object.assign({}, urlQuery, {search: form.search})), {
  //   // onFinish: () => form.reset('password'),
  // });
};

// const search = getUrlQueryParamsAsObj(['search'], true);
// console.log(search)

</script>

<template>

  <form @submit.prevent="submit">
    <div class="input-group">
      <input v-model="form.search" class="form-control" type="text" placeholder="Enter search term..." required>
      <button class="btn btn-primary" id="button-search" type="submit">
        <i class="bi bi-search"></i>
      </button>
    </div>
  </form>

</template>