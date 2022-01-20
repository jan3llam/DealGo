<template>
    <Head title="Register"/>

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo/>
        </template>

        <jet-validation-errors class="mb-4"/>

        <form @submit.prevent="submit">
            <div>
                <jet-label for="name" value="Name"/>
                <jet-input id="name" v-model="form.name" autocomplete="name" autofocus class="mt-1 block w-full"
                           required type="text"/>
            </div>

            <div class="mt-4">
                <jet-label for="email" value="Email"/>
                <jet-input id="email" v-model="form.email" class="mt-1 block w-full" required type="email"/>
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password"/>
                <jet-input id="password" v-model="form.password" autocomplete="new-password" class="mt-1 block w-full"
                           required type="password"/>
            </div>

            <div class="mt-4">
                <jet-label for="password_confirmation" value="Confirm Password"/>
                <jet-input id="password_confirmation" v-model="form.password_confirmation" autocomplete="new-password"
                           class="mt-1 block w-full" required type="password"/>
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <jet-label for="terms">
                    <div class="flex items-center">
                        <jet-checkbox id="terms" v-model:checked="form.terms" name="terms"/>

                        <div class="ml-2">
                            I agree to the <a :href="route('terms.show')"
                                              class="underline text-sm text-gray-600 hover:text-gray-900"
                                              target="_blank">Terms of Service</a> and <a :href="route('policy.show')"
                                                                                          class="underline text-sm text-gray-600 hover:text-gray-900"
                                                                                          target="_blank">Privacy
                            Policy</a>
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Already registered?
                </Link>

                <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-4">
                    Register
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
import {defineComponent} from 'vue'
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import {Head, Link} from '@inertiajs/inertia-vue3';

export default defineComponent({
    components: {
        Head,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors,
        Link,
    },

    data() {
        return {
            form: this.$inertia.form({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                terms: false,
            })
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('register'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            })
        }
    }
})
</script>
