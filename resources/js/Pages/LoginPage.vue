<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { useForm,  } from '@inertiajs/inertia-vue3'
import { cn } from '@/lib/utils';
import route from 'ziggy-js'

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.post'));
}
</script>

<template>
  <Head>
    <title>Entrar</title>
    <meta
      name="description"
      content="Entrar"
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
            Entrar no sistema
          </h1>
        </div>
        <div class="space-y-4">
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
            <div class="grid-flow-col space-y-2">
              <Checkbox
                id="remember"
                v-model="form.remember"
                :class="cn(!!form.errors.remember ? 'border-red-500' : '')"
              />
              <label
                :class="cn(!!form.errors.remember ? 'text-red-500' : '')"
                htmlFor="remember"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
              >
                Manter login
              </label>
            </div>
            <span class="text-red-500 text-sm">{{ form.errors.remember }}</span>
          </div>
          <div class="w-full flex justify-end">
            <Link
              class="text-sm"
              :href="route('register.index')"
            >
              Cadastrar agora!
            </Link>
          </div>
          <Button class="w-full">
            <Loader2Icon
              class="mr-2 h-4 w-4 animate-spin hidden data-[show=true]:inline"
              :data-show="form.processing"
            />
            Entrar
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>