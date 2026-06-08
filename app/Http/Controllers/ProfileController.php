<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Group;

class ProfileController extends Controller
{
    public function wizard()
    {
        $user = auth()->user();
        $group = Group::find(session('current_group_id'));

        if ($user->profile_completed && $user->terms_accepted) {
            return redirect()->route('dashboard');
        }

        return view('profile.wizard', compact('user', 'group'));
    }

    public function storeWizard(Request $request)
    {
        $user = auth()->user();
        $step = $request->input('step', 1);

        switch ($step) {
            case 1:
                $validated = $request->validate([
                    'first_name' => 'required|string|max:255',
                    'middle_name' => 'nullable|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'gender' => 'required|in:male,female,other',
                    'phone_number' => 'required|string|max:20',
                    'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                ]);
                break;

            case 2:
                $validated = $request->validate([
                    'region_id' => 'required|exists:regions,id',
                    'district_id' => 'required|exists:districts,id',
                    'ward_id' => 'required|exists:wards,id',
                    'village_id' => 'required|exists:villages,id',
                    'region' => 'required|string|max:255',
                    'district' => 'required|string|max:255',
                    'ward' => 'required|string|max:255',
                    'village' => 'required|string|max:255',
                    'street' => 'nullable|string|max:255',
                    'cell_leader_name' => 'nullable|string|max:255',
                    'cell_leader_phone' => 'nullable|string|max:20',
                    'lg_chairperson_name' => 'nullable|string|max:255',
                    'lg_chairperson_phone' => 'nullable|string|max:20',
                ]);
                break;

            case 3:
                $validated = $request->validate([
                    'guarantor_name' => 'required|string|max:255',
                    'guarantor_phone' => 'required|string|max:20',
                    'guarantor_relationship' => 'required|string|max:255',
                ]);
                break;

            case 4:
                $validated = $request->validate([
                    'marital_status' => 'required|in:single,married,divorced,widowed',
                    'spouse_name' => 'required_if:marital_status,married|string|nullable|max:255',
                    'spouse_phone' => 'required_if:marital_status,married|string|nullable|max:20',
                    'spouse_occupation' => 'required_if:marital_status,married|string|nullable|max:255',
                    'dependents' => 'nullable|array',
                    'dependents.*.full_name' => 'required_with:dependents|string',
                    'dependents.*.phone' => 'nullable|string',
                    'dependents.*.relationship' => 'required_with:dependents|string',
                ]);
                break;

            case 5:
                $validated = $request->validate([
                    'inheritors' => 'nullable|array',
                    'inheritors.*.full_name' => 'required_with:inheritors|string',
                    'inheritors.*.phone' => 'nullable|string',
                    'inheritors.*.relationship' => 'required_with:inheritors|string',
                ]);
                break;

            case 6:
                $validated = $request->validate([
                    'terms_accepted' => 'required|accepted',
                ]);
                $validated['profile_completed'] = true;
                break;

            default:
                return redirect()->back()->with('error', 'Invalid step.');
        }

        if (isset($validated['dependents'])) {
            $validated['dependents'] = array_values($validated['dependents']);
        }
        if (isset($validated['inheritors'])) {
            $validated['inheritors'] = array_values($validated['inheritors']);
        }

        $user->update($validated);

        if ($step < 6) {
            return redirect()->route('profile.wizard', ['step' => $step + 1])
                ->with('success', 'Step ' . $step . ' saved. Please continue.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Profile completed successfully! Welcome to VICOBRIDGE.');
    }

    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'profile_picture' => 'nullable|image|max:2048',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'ward_id' => 'nullable|exists:wards,id',
            'village_id' => 'nullable|exists:villages,id',
            'region' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'cell_leader_name' => 'nullable|string|max:255',
            'cell_leader_phone' => 'nullable|string|max:20',
            'lg_chairperson_name' => 'nullable|string|max:255',
            'lg_chairperson_phone' => 'nullable|string|max:20',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:20',
            'guarantor_relationship' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_phone' => 'nullable|string|max:20',
            'spouse_occupation' => 'nullable|string|max:255',
            'dependents' => 'nullable|array',
            'inheritors' => 'nullable|array',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $validated['profile_picture'] = $path;
        }

        if (isset($validated['dependents'])) {
            $validated['dependents'] = array_values($validated['dependents']);
        }
        if (isset($validated['inheritors'])) {
            $validated['inheritors'] = array_values($validated['inheritors']);
        }

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }
}
