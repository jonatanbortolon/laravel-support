<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { useForm } from '@inertiajs/inertia-vue3';
import { cn } from '@/lib/utils';
import { Loader2Icon } from 'lucide-vue-next';
import route from 'ziggy-js';

const form = useForm({
    name: '',
    document: '',
    email: '',
    password: '',
    role: 'client'
});

function submit() {
    form.post(route('register.post'));
}
</script>

<template>
  <Head>
    <title>Cadastrar</title>
    <meta
      name="description"
      content="Cadastrar"
    >
  </Head>
  <div class="w-full h-full flex bg-gray-100 items-center justify-center">
    <div class="w-full max-w-lg rounded-lg shadow-lg bg-white p-6 space-y-6 border border-gray-200">
      <form
        class="grid grid-flow-row space-y-4"
        @submit.prevent="submit"
      >
        <div class="space-y-2 text-center">
          <h1 class="text-3xl font-bold">
            Cadastrar
          </h1>
        </div>
        <div class="space-y-4">
          <div class="space-y-2">
            <Label
              :class="cn(!!form.errors.name ? 'text-red-500' : '')"
              html-for="name"
            >Nome completo</Label>
            <Input
              id="name"
              v-model="form.name"
              :class="cn(!!form.errors.name ? 'border-red-500' : '')"
              required
            />
            <span class="text-red-500 text-sm">{{ form.errors.name }}</span>
          </div>
          <div class="space-y-2">
            <Label
              :class="cn(!!form.errors.document ? 'text-red-500' : '')"
              html-for="document"
            >CPF</Label>
            <Input
              id="document"
              v-model="form.document"
              :class="cn(!!form.errors.document ? 'border-red-500' : '')"
              required
              type="number"
            />
            <span class="text-red-500 text-sm">{{ form.errors.document }}</span>
          </div>
          <div class="space-y-2">
            <Label
              :class="cn(!!form.errors.email ? 'text-red-500' : '')"
              html-for="email"
            >Email</Label>
            <Input
              id="email"
              v-model="form.email"
              :class="cn(!!form.errors.email ? 'border-red-500' : '')"
              placeholder="m@example.com"
              required
              type="email"
            />
            <span class="text-red-500 text-sm">{{ form.errors.email }}</span>
          </div>
          <div class="space-y-2">
            <Label
              :class="cn(!!form.errors.password ? 'text-red-500' : '')"
              html-for="password"
            >Senha</Label>
            <Input
              id="password"
              v-model="form.password"
              :class="cn(!!form.errors.password ? 'border-red-500' : '')"
              required
              type="password"
            />
            <span class="text-red-500 text-sm">{{ form.errors.password }}</span>
          </div>
          <div class="space-y-2">
            <Label
              :class="cn(!!form.errors.role ? 'text-red-500' : '')"
              html-for="role"
            >Cargo</Label>
            <Select
              id="role"
              v-model="form.role"
            >
              <SelectTrigger :class="cn(!!form.errors.role ? 'border-red-500' : '')">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="client">
                  Cliente
                </SelectItem>
                <SelectItem value="employee">
                  Colaborador
                </SelectItem>
              </SelectContent>
            </Select>
            <span class="text-red-500 text-sm">{{ form.errors.role }}</span>
          </div>
          <div class="w-full flex justify-end">
            <Link
              class="text-sm"
              :href="route('login.index')"
            >
              Entrar agora!
            </Link>
          </div>
          <Button
            class="w-full"
            :disabled="form.processing"
          >
            <Loader2Icon
              class="mr-2 h-4 w-4 animate-spin hidden data-[show=true]:inline"
              :data-show="form.processing"
            />
            Cadastrar
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>