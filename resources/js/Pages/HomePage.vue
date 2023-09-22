<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Inertia } from '@inertiajs/inertia';

export default {
  layout: AppLayout
}
</script>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import route from 'ziggy-js';
import { Calendar } from '@/components/ui/calendar';
import { CalendarIcon } from 'lucide-vue-next';
import { format } from 'date-fns';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { ChevronLeftIcon } from 'lucide-vue-next';
import { ChevronRightIcon } from 'lucide-vue-next';
import { ChevronsRightIcon } from 'lucide-vue-next';
import { ChevronsLeftIcon } from 'lucide-vue-next';
import { RequestState } from '@/types/requestState';
import { RequestListItem } from '@/props/requestListItem';
import { computed } from 'vue';
import { PageProps } from '@/types/pageProps';

const props = defineProps({
  requests: {
    type: Array<RequestListItem>,
    default: []
  },
  count: {
    type: Number,
    default: 0
  }
})

const page = usePage<PageProps>();

const userRole = computed(() => page.props.value.auth.user.role)
const userId = computed(() => page.props.value.auth.user.id)

const initialQueryString = page.url.value.split('?')[1] ?? ''

const initialQueryParams = new URLSearchParams(initialQueryString)

const pageCountRaw = initialQueryParams.get('page');
const pageCount = pageCountRaw ? (parseInt(pageCountRaw) ?? 1) : 1;

const filtersForm = useForm<{
  state: '' | RequestState;
  date: '' | Date
  from: string
}>({
    state: '',
    date: '',
    from: '',
});

filtersForm.defaults(
  'state',
  initialQueryParams.get('state') ?? 'all',
);

filtersForm.defaults(
  'date',
  initialQueryParams.get('date') ?? ''
);

filtersForm.defaults(
  'from',
  initialQueryParams.get('from') ?? ''
);

filtersForm.reset();

function filtersSubmit() {
  filtersForm.get(route('requests.index'))
}

function requestStateToText(state: RequestState) {
  const states: {[state in RequestState]: string} = {
    open: 'Aberto',
    inprogress: 'Em atendimento',
    closed: 'Fechado'
  }

  return states[state];
}

function firstPage() {
  Inertia.visit(route('requests.index', {
    state: filtersForm.state,
    date: filtersForm.date.toString(),
    page: 1,
  }));
}

function previousPage() {
  Inertia.visit(route('requests.index', {
    state: filtersForm.state,
    date: filtersForm.date.toString(),
    page: pageCount - 1,
  }));
}

function nextPage() {
  Inertia.visit(route('requests.index', {
    state: filtersForm.state,
    date: filtersForm.date.toString(),
    page: pageCount + 1,
  }));
}

function lastPage() {
  Inertia.visit(route('requests.index', {
    state: filtersForm.state,
    date: filtersForm.date.toString(),
    page: Math.ceil(props.count / 2),
  }));
}
</script>

<template>
  <Head>
    <title>Chamados</title>
    <meta
      name="description"
      content="Seus chamados"
    >
  </Head>
  <aside class="w-64 border-r bg-white border-zinc-200 overflow-auto">
    <nav class="flex flex-col gap-4 p-4">
      <h2 class="text-lg font-semibold text-zinc-500">
        Filtros
      </h2>
      <div class="flex flex-col gap-2">
        <form
          class="space-y-4"
          @submit.prevent="filtersSubmit"
        >
          <div class="space-y-2">
            <Label html-for="state">Estado</Label>
            <Select
              v-model="filtersForm.state"
              name="state"
            >
              <SelectTrigger>
                <SelectValue placeholder="Selecione o estado" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">
                  Todos
                </SelectItem>
                <SelectItem value="open">
                  Aberto
                </SelectItem>
                <SelectItem value="inprogress">
                  Em atendimento
                </SelectItem>
                <SelectItem value="closed">
                  Finalizado
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="space-y-2">
            <Label html-for="date">Data</Label>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  class="w-full justify-start font-normal"
                  :variant="'outline'"
                >
                  <CalendarIcon class="mr-2 h-4 w-4" />
                  <span>{{ filtersForm.date ? format(new Date(filtersForm.date), "dd/MM/yyy") : "Selecione uma data" }}</span>
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <Calendar
                  id="date"
                  v-model="filtersForm.date"
                />
              </PopoverContent>
            </Popover>
          </div>
          <div
            v-if="userRole === 'employee'"
            class="space-y-2"
          >
            <Label html-for="from">Nome</Label>
            <Input
              id="from"
              v-model="filtersForm.from"
            />
          </div>
          <Button class="w-full">
            Filtrar
          </Button>
        </form>
        <Link :href="route('requests.index')">
          <Button
            class="w-full"
            variant="ghost"
          >
            Limpar filtros
          </Button>
        </Link>
      </div>
    </nav>
  </aside>
  <main class="flex-1 overflow-auto p-6">
    <div class="w-full min-h-full flex flex-col rounded-lg shadow-sm bg-white p-6 border border-gray-200">
      <div class="w-full flex">
        <h1 class="text-2xl font-semibold text-zinc-950 mb-4">
          Chamados
        </h1>
        <Link
          class="ml-auto"
          :href="route('requests.create')"
        >
          <Button
            v-if="userRole === 'client'"
          >
            Abrir chamado
          </Button>
        </Link>
      </div>
      <div
        v-if="props.requests.length === 0"
        class="w-full flex my-6"
      >
        <h1
        
          class="text-zinc-600 mx-auto"
        >
          Você não possui nenhum chamado
        </h1>
      </div>
      <div class="grid grid-flow-row space-y-2 my-6">
        <Link
          v-for="request in props.requests"
          :key="request.id"
          :href="route('requests.detail', { id: request.id })"
        >
          <div
            class="w-full h-20 flex flex-col justify-between p-3 rounded-lg border border-zinc-200"
          >
            <span class="text-lg text-black/60">
              {{ requestStateToText(request.state) + ' - ' + request.title }}
            </span>
            <span class="text-sm text-black/80">
              {{ format(new Date(request.date), 'dd/MM/yyyy') }} - {{ request.from.id === userId ? 'Você' : request.from.name }}</span>
          </div>
        </Link>
      </div>
      <div class="w-full flex justify-end items-center space-x-6 mt-auto">
        <div class="flex w-[100px] items-center justify-center text-sm font-medium">
          Página {{ pageCount ?? 1 }} de
          {{ Math.max(Math.ceil(props.count / 10), 1) }}
        </div>
        <div class="flex items-center space-x-2">
          <Button
            variant="outline"
            class="h-8 w-8 p-0"
            :disabled="(pageCount ?? 1) <= 1"
            @click="firstPage()"
          >
            <span class="sr-only">Ir para primeira página</span>
            <ChevronsLeftIcon class="h-4 w-4" />
          </Button>
          <Button
            variant="outline"
            class="h-8 w-8 p-0"
            :disabled="(pageCount ?? 1) === 1"
            @click="previousPage()"
          >
            <span class="sr-only">Ir para página anterior</span>
            <ChevronLeftIcon class="h-4 w-4" />
          </Button>
          <Button
            variant="outline"
            class="h-8 w-8 p-0"
            :disabled="((pageCount ?? 1) * 10) >= props.count"
            @click="nextPage()"
          >
            <span class="sr-only">Ir para próxima página</span>
            <ChevronRightIcon class="h-4 w-4" />
          </Button>
          <Button
            variant="outline"
            class="h-8 w-8 p-0"
            :disabled="((pageCount ?? 1) * 10) >= props.count"
            @click="lastPage()"
          >
            <span class="sr-only">Ir para última página</span>
            <ChevronsRightIcon class="h-4 w-4" />
          </Button>
        </div>
      </div>
    </div>
  </main>
</template>