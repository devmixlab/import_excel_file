<script setup>
import { reactive, computed, ref, onMounted } from 'vue'

const modal = ref();
let myModal;

const data = reactive({});

const open = (postCode) => {
  axios.get('/api/postindex/' + postCode).then((response) => {
    data.value = response.data.data;
    myModal.show();
  });
}

onMounted(() => {
  myModal = new window.bootstrap.Modal(modal.value, {
    keyboard: false
  })
});

defineExpose({
  open
})

const keysMapTitle = {
  post_code_of_post_office: "Поштовий індекс відділення зв`язку (Post code of post office)",
  region: "Область",
  district_old: "Район (старий)",
  district_new: "Район (новий)",
  settlement: "Населений пункт",
  postal_code: "Поштовий індекс (Postal code)",
  region_en: "Region (Oblast)",
  district_new_en: "District new (Raion new)",
  settlement_en: "Settlement",
  post_office: "Вiддiлення зв`язку",
  post_office_en: "Post office",
}

const keysMapTitleArr = computed(() => {
  return Object.keys(keysMapTitle);
});


</script>

<template>

  <div ref="modal" class="modal modal-lg" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Info:</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
<!--          {{data.value}}-->
          <div class="for-table" v-if="data.value">
            <table class="table table-hover">
              <tbody>
                <template v-for="key in keysMapTitleArr">
  <!--                {{data.value["post_code_of_post_office"]}}-->
                  <tr v-if="data.value[key]">
                    <td class="bg-light px-3">
                      {{keysMapTitle[key]}}
                    </td>
                    <td class="px-3">{{data.value[key]}}</td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
          <div v-else>
            No data found.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

</template>

<style lang="scss" scoped>
  table{
    //border: 1px solid #ff1f1;
    //border-radius: 5px;
    margin-bottom: 0px;
    tr:last-child td{
      border-bottom: none;
    }
    td:first-child{
      width: 30%;
    }
  }
  .for-table{
    border: 1px solid #e9ecef;
    border-radius: 5px;
    overflow: hidden;
  }
</style>