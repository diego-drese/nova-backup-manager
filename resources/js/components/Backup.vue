<template>
    <tr :class="{ 'is-deleting': deleting }">
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ path }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ formatDate(date) }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ size }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 text-right">
            <a
                v-if="canDownload"
                :href="downloadUrl"
                target="_blank"
                rel="noopener nofollow"
                :title="__('Download')"
                class="inline-flex items-center justify-center text-70 hover:text-primary mr-3"
            >
                <Icon name="arrow-down-on-square" />
            </a>

            <button
                v-if="deletable"
                :title="__('Delete')"
                class="inline-flex items-center justify-center mr-3"
                :class="deletable ? 'text-70 hover:text-primary' : 'cursor-default text-40'"
                :disabled="!deletable"
                @click.prevent="$emit('delete')"
            >
                <Icon name="trash" />
            </button>
        </td>

    </tr>
</template>

<script setup>
import { Icon } from 'laravel-nova-ui';
import { computed } from 'vue';

const formatDate = (rawDate) => {
    const date = new Date(rawDate + 'Z'); // força UTC
    return new Intl.DateTimeFormat(undefined, {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZoneName: 'short',
        hour12: true, // para AM/PM
    }).format(date);
};

const props = defineProps({
    date: { required: true },
    path: { required: true },
    size: { required: true },
    disk: { required: true },
    deletable: { required: true },
    deleting: { required: true },
    canDownload: { type: Boolean, default: true },
});
const downloadUrl = computed(() => {
    const endpoint = '/nova/nova-backup-manager/download-backup';
    return `${endpoint}?disk=${props.disk}&path=${props.path}`;
});
</script>

<style scoped>
.is-deleting td {
    color: var(--60);
}
</style>
