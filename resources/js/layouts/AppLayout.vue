<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { LibraryIcon } from 'lucide-vue-next';
import { LogOutIcon } from 'lucide-vue-next';
import route from 'ziggy-js';
</script>

<template>
  <div class="flex flex-col h-screen w-screen bg-white">
    <nav class="flex items-center justify-between px-6 py-4 border-b border-zinc-200">
      <div class="flex">
        <Link
          class="ml-auto flex items-center space-x-4"
          :href="route('requests.index')"
        >
          <LibraryIcon
            class=" h-8 w-8 text-zinc-900"
          />
          <h1 class="text-xl font-semibold text-zinc-900">
            Laravel Support
          </h1>
        </Link>
      </div>
      <div class="flex items-center space-x-4">
        <Link :href="route('logout.index')">
          <Button
            size="icon"
            variant="ghost"
          >
            <LogOutIcon
              :size="20"
              class="text-red-500"
            />
            <span class="sr-only">Sair</span>
          </Button>
        </Link>
      </div>
    </nav>
    <div class="flex flex-1 overflow-hidden bg-zinc-300">
      <slot />
    </div>
    <footer class="flex items-center justify-between px-6 py-4 border-t border-zinc-200">
      <p class="text-sm text-zinc-500">
        © 2023 Laravel Support.
      </p>
      <nav class="flex items-center space-x-4">
        <span
          class-name="text-sm text-zinc-500"
        >
          Termos
        </span>
        <span
          class-name="text-sm text-zinc-500"
        >
          Privacidade
        </span>
      </nav>
    </footer>
  </div>
</template>