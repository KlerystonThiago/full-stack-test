<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { dashboard } from '@/routes';
  import type { BreadcrumbItemType } from '@/types';
  import { Link } from '@inertiajs/vue3';
  import { Users2, Handshake, FileMinus } from 'lucide-vue-next';
  import NavFooter from '@/components/NavFooter.vue';
  import NavUser from '@/components/NavUser.vue';
  import AppSidebarHeader from '@/components/AppSidebarHeader.vue';

  import AppContent from '@/components/AppContent.vue';
  import AppShell from '@/components/AppShell.vue';

  import NavMain from '@/components/NavMain.vue';
  import {
      Sidebar,
      SidebarContent,
      SidebarHeader,
      SidebarMenu,
      SidebarMenuButton,
      SidebarMenuItem,
      SidebarFooter
  } from '@/components/ui/sidebar';
  import { type NavItem } from '@/types';
  import AppLogo from '@/components/AppLogo.vue';
  import admin from '@/routes/admin';

  const mainNavItems: NavItem[] = [
      {
          title: 'Usuários',
          href: admin.users.index(),
          icon: Users2,
      },
      {
          title: 'Clientes',
          href: admin.customers.index(),
          icon: Handshake,
      },
      {
          title: 'Invoices',
          href: admin.invoices.index(),
          icon: FileMinus,
      },
  ];

  interface Props {
      breadcrumbs?: BreadcrumbItemType[];
  }

  withDefaults(defineProps<Props>(), {
      breadcrumbs: () => [],
  });
</script>

<template>
    <AppShell variant="sidebar">
      <Sidebar collapsible="icon" variant="inset">
          <SidebarHeader>
              <SidebarMenu>
                  <SidebarMenuItem>
                      <SidebarMenuButton size="lg" as-child>
                          <Link :href="dashboard()">
                              <AppLogo />
                          </Link>
                      </SidebarMenuButton>
                  </SidebarMenuItem>
              </SidebarMenu>
          </SidebarHeader>

          <SidebarContent>
              <NavMain :items="mainNavItems" />
          </SidebarContent>

          <SidebarFooter>
            <NavUser />
        </SidebarFooter>
      </Sidebar>
      <AppContent variant="sidebar" class="overflow-x-hidden">
        <AppSidebarHeader />


          <slot />
      </AppContent>
    </AppShell>
</template>
