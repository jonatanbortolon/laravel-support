<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

export default {
  layout: AppLayout
}
</script>

<script setup lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Loader2Icon } from 'lucide-vue-next';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import route from 'ziggy-js';

const form = useForm({
    title: '',
    description: '',
    attachments: []
});

function submit() {
  form.post(route('requests.post'), {
    forceFormData: true
  })
}
</script>

<template>
  <Head>
    <title>Abrir chamado</title>
    <meta
      name="description"
      content="Abrir chamado"
    >
  </Head>
  <main class="flex-1 overflow-auto p-6">
    <div class="w-full mx-auto rounded-lg shadow-sm max-w-lg bg-white p-6 space-y-6 border border-gray-200">
      <form
        class="grid space-y-4"
        @submit.prevent="submit"
      >
        <div class="space-y-2">
          <Label
            :class="cn(!!form.errors.title ? 'text-red-500' : '')"
            html-for="title"
          >Título</Label>
          <Input
            id="title"
            v-model="form.title"
            :class="cn(!!form.errors.title ? 'border-red-500' : '')"
            required
          />
          <span class="text-red-500 text-sm">{{ form.errors.title }}</span>
        </div>
        <div class="space-y-2">
          <Label
            :class="cn(!!form.errors.description ? 'text-red-500' : '')"
            html-for="description"
          >Descrição</Label>
          <Textarea
            id="description"
            v-model="form.description"
            :class="cn(!!form.errors.description ? 'border-red-500' : '')"
            required
          />
          <span class="text-red-500 text-sm">{{ form.errors.description }}</span>
        </div>
        <div class="space-y-2">
          <Label
            :class="cn(!!form.errors.attachments ? 'text-red-500' : '')"
            html-for="attachments"
          >Anexos <span class="italic text-xs">- Opcional</span></Label>
          <Input
            id="attachments"
            accept=".csv,.txt,.xlx,.xls,.pdf,.jpg,.jpeg,.png,.bmp"
            :class="cn(!!form.errors.attachments ? 'border-red-500' : '')"
            type="file"
            multiple
            @input="form.attachments = $event.target.files"
          />
          <!-- <Progress
            v-if="form.progress"
            :model-value="form.progress.percentage ?? 0"
          /> -->
          <span class="text-red-500 text-sm">{{ form.errors.attachments }}</span>
        </div>
        <Button class="w-full">
          <Loader2Icon
            class="mr-2 h-4 w-4 animate-spin hidden data-[show=true]:inline"
            :data-show="form.processing"
          />
          Abrir chamado
        </Button>
      </form>
    </div>
  </main>
</template>