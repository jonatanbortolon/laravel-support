<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { PageProps } from '@/types/pageProps';

export default {
  layout: AppLayout
}
</script>

<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import { Input } from '@/components/ui/input';
import { Loader2Icon } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { RequestState } from '@/types/requestState';
import { cn } from '@/lib/utils';
import route from 'ziggy-js';
import { Button } from '@/components/ui/button';
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { RequestDetail } from '@/props/requestDetail';
import { RequestDetailComments } from '@/props/requestDetailComments';
import { format } from 'date-fns';

const props = defineProps({
  request: {
    type: RequestDetail,
    default: null,
  },
  comments: {
    type: Array<RequestDetailComments>,
    default: [],
  }
})

const page = usePage<PageProps>();

const userRole = computed(() => page.props.value.auth.user.role)

const updateForm = useForm({
    state: 'open',
});

updateForm.defaults(
  'state',
  props.request.state
);

updateForm.reset();

const commentForm = useForm({
    content: '',
    attachments: []
});

function updateSubmit() {
  updateForm.put(route('requests.put', {
    id: props.request.id
  }))
}

function commentSubmit() {
  commentForm.post(route('requests.comments.post', {
    id: props.request.id
  }), {
    forceFormData: true
  })

  commentForm.reset();
}

function requestStateToText(state: RequestState) {
  const states: {[state in RequestState]: string} = {
    open: 'Aberto',
    inprogress: 'Em atendimento',
    closed: 'Fechado'
  }

  return states[state];
}
</script>

<template>
  <Head>
    <title>{{ props.request.title }}</title>
    <meta
      name="description"
      :content="'Chamado - ' + props.request.title"
    >
  </Head>
  <main class="flex-1 space-y-4 overflow-auto p-6">
    <div class="w-full mx-auto rounded-lg shadow-sm max-w-lg bg-white space-y-6 border border-gray-200">
      <div class="flex flex-col border-b border-b-zinc-100 p-6">
        <h1 class="text-2xl font-semibold text-zinc-950 mb-4">
          {{ props.request.title }}
        </h1>
        <form
          v-if="userRole === 'employee'"
          class="grid grid-flow-row space-y-4"
          @submit.prevent="updateSubmit()"
        >
          <div class="space-y-2">
            <Label
              :class="cn(!!updateForm.errors.state ? 'text-red-500' : '')"
              html-for="state"
            >Cargo</Label>
            <Select
              id="state"
              v-model="updateForm.state"
            >
              <SelectTrigger :class="cn(!!updateForm.errors.state ? 'border-red-500' : '')">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
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
            <span class="text-red-500 text-sm">{{ updateForm.errors.state }}</span>
          </div>
          <Button
            class="w-full"
            :disabled="updateForm.processing"
          >
            <Loader2Icon
              class="mr-2 h-4 w-4 animate-spin hidden data-[show=true]:inline"
              :data-show="updateForm.processing"
            />
            Atualizar
          </Button>
        </form>
        <span v-if="userRole !== 'employee'">Estado: {{ requestStateToText(props.request.state) }}</span>
      </div>
      <div class="w-full flex flex-col gap-4">
        <div
          v-for="comment in props.comments"
          :key="comment.date"
          class="w-full p-6 flex flex-col gap-2"
        >
          <div class="w-full flex justify-between">
            <span class="text-sm text-zinc-800">{{ comment.from }}</span>
            <span class="text-sm text-zinc-800">{{ format(new Date(comment.date), 'dd/MM/yyyy HH:mm') }}</span>
          </div>
          <span class="whitespace-pre-wrap">{{ comment.content }}</span>
          <Accordion
            type="single"
            collapsible
          >
            <AccordionItem value="attachments">
              <AccordionTrigger class="py-2">
                Anexos
              </AccordionTrigger>
              <AccordionContent>
                <ul>
                  <li
                    v-for="attachment in comment.attachments"
                    :key="attachment"
                  >
                    <a
                      :href="'/storage/attachments/' + attachment"
                      target="_blank"
                    >
                      <Button variant="link">
                        {{ attachment }}
                      </Button>
                    </a>
                  </li>
                </ul>
              </AccordionContent>
            </AccordionItem>
          </Accordion>
        </div>
      </div>
    </div>
    <div class="w-full mx-auto rounded-lg shadow-sm max-w-lg bg-white space-y-6 border border-gray-200">
      <form
        class="grid space-y-4 p-6"
        @submit.prevent="commentSubmit"
      >
        <h2 class="text-lg font-semibold text-zinc-500">
          Novo comentário
        </h2>
        <div class="space-y-2">
          <Label
            :class="cn(!!commentForm.errors.content ? 'text-red-500' : '')"
            html-for="content"
          >Descrição</Label>
          <Textarea
            id="content"
            v-model="commentForm.content"
            :class="cn(!!commentForm.errors.content ? 'border-red-500' : '')"
            required
          />
          <span class="text-red-500 text-sm">{{ commentForm.errors.content }}</span>
        </div>
        <div class="space-y-2">
          <Label
            :class="cn(!!commentForm.errors.attachments ? 'text-red-500' : '')"
            html-for="attachments"
          >Anexos <span class="italic text-xs">- Opcional</span></Label>
          <Input
            id="attachments"
            accept=".csv,.txt,.xlx,.xls,.pdf,.jpg,.jpeg,.png,.bmp"
            :class="cn(!!commentForm.errors.attachments ? 'border-red-500' : '')"
            type="file"
            multiple
            @input="commentForm.attachments = $event.target.files"
          />
          <!-- <Progress
            v-if="commentForm.progress"
            :model-value="commentForm.progress.percentage ?? 0"
          /> -->
          <span class="text-red-500 text-sm">{{ commentForm.errors.attachments }}</span>
        </div>
        <Button class="w-full">
          <Loader2Icon
            class="mr-2 h-4 w-4 animate-spin hidden data-[show=true]:inline"
            :data-show="commentForm.processing"
          />
          Enviar comentário
        </Button>
      </form>
    </div>
  </main>
</template>