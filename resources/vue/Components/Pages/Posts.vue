<script setup>
import List from '@components/Partials/List/List.vue';
import Column from '@components/Partials/List/Column.vue';
import HeadColumn from '@components/Partials/List/HeadColumn.vue';
import Row from '@components/Partials/List/Row.vue';
import { reactive, ref, onUpdated, computed } from 'vue'
import Pagination from '@components/Partials/List/Pagination.vue';
import Modal from '@components/Partials/Modal.vue';

const paginator = reactive({value: null});
const perPage = reactive({value: null});

const search = defineModel()

let currentPageUrl =  new URL(window.location.origin + window.location.pathname);
let apiUrl = new URL(window.location.origin + '/api/postindex');
apiUrl.search = window.location.search;

if(apiUrl.searchParams.has("settlement"))
  search.value = apiUrl.searchParams.get("settlement");


onLinkClick(apiUrl.href);

const modalEdit = ref();

onUpdated(() => {
  perPage.value = apiUrl.searchParams.get("per_page");
})

function onClickView(postCode) {
  modalEdit.value.open(postCode)
}

function onClickDelete(postCode) {
  if(!confirm("Are you sure?"))
    return;

  axios.delete('/api/postindex/' + postCode).then((response) => {
    onLinkClick(apiUrl.href);
  });
}

function onLinkClick(url) {
  axios.get(url).then((response) => {
    paginator.value = response.data;

    // console.log(response.data)
    if(response.data.current_page.includes("settlement"))
      isSearch.value = true;

    let result = paginator.value.pages.find(obj => {
      return obj.url == response.data.current_page;
    });

    if(typeof result === 'undefined' && response.data.previous_page !== null) {
      onLinkClick(response.data.previous_page);
      return;
    }

    const respondCurrentPageUrl = new URL(response.data.current_page);

    apiUrl.search = respondCurrentPageUrl.search;
    currentPageUrl.search = respondCurrentPageUrl.search;

    window.history.replaceState({}, '', currentPageUrl.href);
  });
}

function onSearch() {
  if(!search.value)
    return;

  apiUrl.searchParams.set('settlement', search.value);
  apiUrl.searchParams.set('page', 1);
  onLinkClick(apiUrl.href)
}

function onPerPageClick(perPage) {
  apiUrl.searchParams.set('per_page', perPage);
  apiUrl.searchParams.set('page', 1);
  onLinkClick(apiUrl.href)
}

function resetSearch() {
  search.value = '';
  isSearch.value = false;
  apiUrl.searchParams.delete('settlement');

  onLinkClick(apiUrl.href)
}

const isSearch = reactive({
  value: false
});


</script>

<template>

  <div class="d-flex justify-content-between pt-3 align-items-bottom">
    <div class="d-flex">
      <h1 class="pt-1">Posts</h1>
      <div class="dropdown pt-3 ms-4">
        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          {{perPage.value}} pages
        </button>
        <ul class="dropdown-menu">
          <template v-for="p in [1,5,10,20,30,50,100]">
            <li v-if="p != perPage.value">
              <a @click.prevent="onPerPageClick(p)" class="dropdown-item" href="#">{{p}} pages</a>
            </li>
          </template>
<!--          <li><a class="dropdown-item" href="#">Another action</a></li>-->
<!--          <li><a class="dropdown-item" href="#">Something else here</a></li>-->
        </ul>
      </div>
      <div class="search pt-3 ps-4">
        <div class="mb-3 d-flex">
          <input v-model="search" type="email" class="form-control form-control-sm me-2" id="exampleInputEmail1">
          <button @click.prevent="onSearch" class="btn btn-sm btn-primary">Search</button>
<!--          {{isSearch}}-->
          <button v-if="isSearch.value" @click.prevent="resetSearch" class="ms-2 btn btn-sm btn-secondary">Reset</button>
        </div>
      </div>
    </div>
    <div class="pt-3">
      <router-link class="btn btn-success btn-sm"
        to="/post/create">Create</router-link>
    </div>
  </div>

  <div class="pb-5" v-if="paginator.value?.data?.length">
    <List :paginator="paginator.value">
      <template #thead="slotProps">
        <Row>
          <HeadColumn style="width: 100px;">Index</HeadColumn>
          <HeadColumn>Вiддiлення зв`язку</HeadColumn>
          <HeadColumn>Область</HeadColumn>
          <HeadColumn>Район (старий)</HeadColumn>
          <HeadColumn>Район (новий)</HeadColumn>
          <HeadColumn>Населений пункт</HeadColumn>
          <HeadColumn class="text-center" style="width: 120px;">Created At</HeadColumn>
          <HeadColumn :style="{color: 'red'}" style="width: 150px;"></HeadColumn>
        </Row>
      </template>
      <template #tbody="slotProps">
        <Row>
          <Column :data="slotProps.data" name="post_code_of_post_office"></Column>
          <Column :data="slotProps.data" name="post_office"></Column>
          <Column :data="slotProps.data" name="region"></Column>
          <Column :data="slotProps.data" name="district_old"></Column>
          <Column :data="slotProps.data" name="district_new"></Column>
          <Column :data="slotProps.data" name="settlement"></Column>
          <Column class="text-center" :data="slotProps.data" name="created_at"></Column>
          <Column>

            <div class="d-flex justify-content-end">
              <a @click.prevent="onClickView(slotProps.data.post_code_of_post_office)" href="#" class="btn btn-sm btn-info me-2">
                view
              </a>
              <a @click.prevent="onClickDelete(slotProps.data.post_code_of_post_office)" href="#" class="btn btn-sm btn-danger">delete</a>
            </div>

          </Column>
        </Row>
      </template>
      <template #pagination="slotProps">
        <Pagination @linkClick="onLinkClick" :paginator="slotProps.paginator" />
      </template>
    </List>

    <Modal ref="modalEdit" />
  </div>
  <div v-else>
    <div class="alert alert-light" role="alert">
      No data
    </div>
  </div>

</template>


<style scoped lang="scss">
  .search{
    max-width: 500px;
  }
</style>