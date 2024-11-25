<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    profile_img: null,
    class: user.class || '',         // Thêm trường class
    student_code: user.student_code || '', // Thêm trường student_code
    phone: user.phone || '',         // Thêm trường phone
});

</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information, email address.
            </p>
        </header>


        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <!-- Hiển thị ảnh đại diện hiện tại nếu có, nếu không sẽ hiển thị ảnh mặc định -->
            <div class="mb-4 mt-4 flex flex-col items-center">
                <InputLabel for="avatar" value="" class="mb-2 text-lg font-semibold text-gray-700" />
                <div class="relative">
                    <img
                        :src="user.profile_img ? `/profile/${user.profile_img}` : '/uploads/profile/default-profile.png'"
                        alt="Avatar"
                        class="w-40 h-40 rounded-full object-cover border-4 border-blue-500 shadow-lg"
                    />
                    <!-- Badge Icon (tuỳ chọn) -->
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 border-2 border-white rounded-full flex items-center justify-center shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707a1 1 0 00-1.414-1.414L9 11.586 7.707 10.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <InputError class="mt-2 text-red-500" :message="form.errors.profile_img" />
            </div>

            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div>
                  <InputLabel for="class" value="Class" />
                  <TextInput
                      id="class"
                      type="text"
                      class="mt-1 block w-full"
                      v-model="form.class"
                      autocomplete="class"
                  />
                  <InputError class="mt-2" :message="form.errors.class" />
              </div>

            <div>
                <InputLabel for="student_code" value="Student Code" />
                <TextInput
                    id="student_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.student_code"
                    autocomplete="student_code"
                />
                <InputError class="mt-2" :message="form.errors.student_code" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    autocomplete="phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>


            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

