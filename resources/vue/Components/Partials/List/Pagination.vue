<template>
  <nav v-if="links">
    <ul class="pagination mb-0">
      <template :key="idx + '-' + link.url" v-for="(link, idx) in links">
        <li class="page-item" :class="{active: link.active}">
          <a @click.prevent="onLinkClick(link.url)" v-if="link.url"
             class="page-link"
             :class="{
               active: link.active
             }"
             :href="link.url"
             v-html="link.label" />
          <span class="page-link disabled" v-else v-html="link.label"></span>
        </li>
      </template>
    </ul>
  </nav>
</template>

<script setup>
import {computed, reactive, watch, defineEmits} from "vue";

const emit = defineEmits(['linkClick'])

function onLinkClick(url) {
  emit('linkClick', url)
}

const props = defineProps({
  paginator: {
    type: Object,
  },
});

const links = computed(() => {
  // console.log(props.paginator)
  return props.paginator?.pages ?? null;
});

// console.log(props.paginator.pages)

function checkPageAvailability() {
  if(!props.paginator.prev_page_url)
    return;

  const {perPage, page} = getUrlQueryParamsAsObj();

  const minLength = perPage * page - perPage + 1;

  // console.log('minLength: ' + minLength)
  // console.log('total: ' + props.paginator.total)

  if(minLength > props.paginator.total)
    router.visit(props.paginator.prev_page_url);
}

checkPageAvailability();
</script>