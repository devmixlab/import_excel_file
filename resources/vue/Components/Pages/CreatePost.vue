<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router'

const router = useRouter();

const item = {
  post_code_of_post_office: {
    key: "post_code_of_post_office",
    label: "Поштовий індекс відділення зв`язку (Post code of post office)",
    value: "",
  },
  region: {
    key: "region", label: "Область", value: "",
  },
  district_old: {
    key: "district_old", label: "Район (старий)", value: "",
  },
  district_new: {
    key: "district_new", label: "Район (новий)", value: "",
  },
  settlement: {
    key: "settlement", label: "Населений пункт", value: "",
  },
  postal_code: {
    key: "postal_code", label: "Поштовий індекс (Postal code)", value: "",
  },
  region_en: {
    key: "region_en", label: "Region (Oblast)", value: "",
  },
  district_new_en: {
    key: "district_new_en", label: "District new (Raion new)", value: "",
  },
  settlement_en: {
    key: "settlement_en", label: "Settlement", value: "",
  },
  post_office: {
    key: "post_office", label: "Вiддiлення зв`язку", value: "",
  },
  post_office_en: {
    key: "post_office_en", label: "Post office", value: "",
  }
}

function onClickRemoveItem(row) {
  let idx = data.indexOf(row);
  data.splice(idx, 1);

  if(errors.value == null)
    return;

  do{
    errors.value[idx] = errors.value[idx + 1]
    idx++;
  }while(errors.value[idx + 1]);
  delete errors.value[idx];
}

const data = reactive([]);
const errors = reactive({value: null});

// data.push(item);

function pushItem() {
  let itm = JSON.parse(JSON.stringify(item));
  data.push(itm);
}

pushItem();

function sendData () {
  let dataToSend = data.map((itm) => {
    let obj = {}
    Object.keys(itm).map((key) => {
      // if(key == )
      obj[key] = itm[key].value;
    });

    return obj;
  })

  axios.post("/api/postindex", dataToSend).then((response) => {
    router.push('/post');
  }).catch((error) => {
    let errs = error.response.data?.errors;
    let errsToSet = {};

    errs.map((itm, idx) => {
      errsToSet[itm.idx_validation] = itm;
    });

    errors.value = errsToSet;
  });
}

</script>

<template>

  <div class="pt-3">
<!--    {{errors}}-->
    <div class="d-flex justify-content-between align-items-bottom">
      <h1>Create Posts</h1>
    </div>

    <template :key="idx" v-for="(row, idx) in data">
      <div class="card mb-3" :class="{
        'border-danger': errors.value && errors.value[idx]
      }">
        <div class="card-header">
          <div class="d-flex justify-content-between">
            <div class="fw-bold">New Post</div>
            <div class="btns-quantity">
              <a @click.prevent="pushItem" class="btn btn-sm btn-success me-2" href="#">+</a>
              <a @click.prevent="onClickRemoveItem(row)" v-if="data.length > 1" class="btn btn-sm btn-danger" href="#">-</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div v-if="
            errors.value &&
            errors.value[idx]?.errors?.allOf &&
            typeof errors.value[idx].errors.allOf === 'string'
          " class="alert alert-danger" role="alert">
            {{errors.value[idx].errors.allOf}}
          </div>
          <div class="row">
            <template :key="idx + '_' + item.key" v-for="item in row">
              <div class="col col-2 mb-3">
                <label for="exampleInputEmail1" class="form-label">{{item.label}}</label>
                <input v-model="item.value" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                <small class="text-danger" v-if="
                  errors.value &&
                  errors.value[idx]?.errors?.allOf &&
                  errors.value[idx].errors.allOf[item.key]
                ">
                  {{ errors.value[idx].errors.allOf[item.key] }}
                </small>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>

    <div class="pt-2 pb-5">
      <a @click.prevent="sendData" class="w-100 btn btn-sm btn-success" href="#">Create</a>
    </div>

  </div>

</template>

<style lang="scss" scoped>
  .btns-quantity a{
    width: 40px;
  }
</style>