<template>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-sm">
        <div class="card mt-2">
          <div class="card-header bg-dark text-white-50">
            <div class="row">
              <div class="col-4 col-sm-4">Email <button class="btn btn-outline-secondary" @click="orderBy('email')">order</button></div>
              <div class="col-5 col-sm-4">Endereço <button class="btn btn-outline-secondary" @click="orderBy('address')">order</button></div>
              <div class="col-2 col-sm-2">Status <button class="btn btn-outline-secondary" @click="orderBy('status')">order</button></div>
              <div class="col-1 col-sm-2 text-center">Ações</div>
            </div>
          </div>
          <div class="card-body" v-for="imovel in imoveis" v-bind:key="imovel.id">
            <div class="row">
              <div class="col-4 col-sm-4">{{  imovel.email }}</div>
              <div class="col-5 col-sm-4">{{ imovel.address +', '+ imovel.number +', '+ imovel.city +', '+ imovel.state }}</div>
              <div class="col-2 col-sm-2">{{ 'Não Contratado' }}</div>
              <div class="col-1 col-sm-2 text-center">
                <button @click="deleteImovel(imovel.id)" type="button" class="btn btn-outline-danger w-20">Remover</button>
              </div>
            </div>
          </div>
          <div class="card-footer bg-dark text-white-50">
            <div class="row">
              <div class="col-11 col-sm-10"></div>
              <div class="col-1 col-sm-2 text-center">
                <button type="button" class="btn btn-outline-primary w-40" @click="showModal">Novo Imovel</button>
              </div>
            </div>
          </div>
          <modal-component v-show="isModalVisible" :csrf_token="csrf_token" @createImovel="createdImovel" @close="closeModal" />
        </div>
        <nav aria-label="Page navigation example">
          <ul class="pagination">
            <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
              <a class="page-link" href="#" @click="fetchImoveis(pagination.prev_page_url)">Anterior</a>
            </li>
            <li class="page-item">
              <a class="page-link" href="#" @click="fetchImoveis(pagination.first_page_url)">{{ pagination.first_page }}</a>
            </li>
            <li class="page-item disabled">
              <a class="page-link text-dark" href="#">Página {{ pagination.current_page }} de {{ pagination.last_page }}</a>
            </li>
            <li class="page-item">
              <a class="page-link" href="#" @click="fetchImoveis(pagination.last_page_url)">{{ pagination.last_page }}</a>
            </li>
            <li class="page-item" v-bind:class="[{disabled: !pagination.next_page_url}]">
              <a class="page-link" href="#" @click="fetchImoveis(pagination.next_page_url)">Próxima</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
import Toasted from 'vue-toasted';
import sortBy from 'sort-by';

Vue.use(Toasted, { position: 'bottom-right', duration: 5000, theme: 'toasted-primary' });

import modal from './Modal.vue';
  export default {
    props: [
      'csrf_token'
    ],
    components: {
      modal,
    },
    data: () => {
      return {
        imoveis: [],
        imovel: {
          id: '',
          email: '',
          state: '',
          city: '',
          address: '',
          number: '',
          complement: '',
          created_at: '',
          updated_at: '',
          deleted_at: ''
        },
        isModalVisible: false,
        pagination: {},
        order: {
          name: '',
          order_by: ''
        }
      }
    },
    created() {
      this.fetchImoveis();
    },
    methods: {
      fetchImoveis(page_url) {
        let vm = this;

        page_url = page_url || '/imovel';

        fetch(page_url)
          .then(res => res.json())
          .then(res => {
              this.imoveis = res.data;
              vm.paginacao(res.meta, res.links);
          })
          .catch(err => console.log(err));
      },
      paginacao(meta, links) {
        let pagination = {
          path: meta.path,
          current_page: meta.current_page,
          first_page: 1,
          first_page_url: links.first,
          last_page: meta.last_page,
          last_page_url: links.last,
          next_page_url: links.next,
          prev_page_url: links.prev
        }

        this.pagination = pagination;
      },            
      createdImovel() {
        this.$toasted.success('Imóvel criado com sucesso')
        this.fetchImoveis();
      },
      deleteImovel(id) {
        if (confirm("Você tem certeza que deseja deletar esse imóvel?")){
          fetch(`/imovel/${id}`, { 
            method: 'DELETE',
            headers: { 
              'X-CSRF-Token': this.csrf_token
            }
          })
          .then(res => res.json())
          .then(data => {
            this.$toasted.success('Imóvel deletado com sucesso')
            this.fetchImoveis();
          })
          .catch(err => console.log(err));
        }
      },
      orderBy(value) {
        if (this.order.order_by === '') {
          this.order = {
            name: value,
            order_by: 'desc'
          }
        } else if (this.order.order_by === 'desc') {
          this.order = {
            name: value,
            order_by: 'asc'
          }
        } else {
          this.order = {
            name: value,
            order_by: 'desc'
          }
        }
        fetch('/imovel/list/' + this.order.name + ',' + this.order.order_by, {
          method: 'GET',
          headers: {
            'content-type': 'application/json',
            'X-CSRF-Token': this.csrf_token
          },
          dataType: 'json'
        })
        .then(res => res.json())
        .then(res => {
          this.imoveis = res.data;
          this.paginacao(res.meta, res.links);
        })
        .catch(err => console.log(err));
        console.log(this.order);
      },
      showModal() {
        this.isModalVisible = true;
      },
      closeModal() {
        this.isModalVisible = false;
      }
    }
  }
</script>
