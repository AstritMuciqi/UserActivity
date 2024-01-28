<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Users from '@/Components/Users.vue';
import UserBalance from "@/Components/UserBalance.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ADMIN PANEL
            </h2>

        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.auth.user.role === 'super_admin'||$page.props.auth.user.role === 'admin'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Users />
                </div>
                <div v-if="$page.props.auth.user.role === 'user'" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <UserBalance />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
