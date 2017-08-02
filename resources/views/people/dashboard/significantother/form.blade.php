@push('scripts')
  <script type="text/javascript">
      $( ".nav-item" ).click(function() {
          $(this).children().addClass('active');
          $(this).siblings().children().removeClass('active');
      });
  </script>
@endpush

  <h2>{{ trans('people.significant_other_add_title', ['name' => $contact->getFirstName()]) }}</h2>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" href="#new" role="tab">
        Add a new person
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-toggle="tab" href="#existing" role="tab">
        Link existing contact
      </a>
    </li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane active" id="new" role="tabpanel">

      <form method="POST" action="{{ $action }}">
        {{ method_field($method) }}
        {{ csrf_field() }}

        @include('partials.errors')

        {{-- First name --}}
        <div class="form-group">
          <label for="first_name">{{ trans('people.significant_other_add_firstname') }}</label>
          <input type="text" class="form-control" name="first_name" id="first_name" maxlength="254" value="{{ old('first_name') ?? $partner->first_name }}" autofocus required>
        </div>

        {{-- Gender --}}
        <label>{{ trans('people.people_add_gender') }}</label>
        <fieldset class="form-group">
          <label class="form-check-inline" for="genderNone">
            <input type="radio" class="form-check-input" name="gender" id="genderNone" value="none" @if(! in_array(old('gender'), ['male', 'female']) || ! in_array($partner->gender, ['male', 'female'])) checked @endif>
            {{ trans('app.gender_none') }}
          </label>

          <label class="form-check-inline" for="genderMale">
            <input type="radio" class="form-check-input" name="gender" id="genderMale" value="male" @if(old('gender') === 'male' || $partner->gender === 'male') checked @endif>
            {{ trans('app.gender_male') }}
          </label>

          <label class="form-check-inline" for="genderFemale">
            <input type="radio" class="form-check-input" name="gender" id="genderFemale" value="female" @if(old('gender') === 'female' || $partner->gender === 'female') checked @endif>
            {{ trans('app.gender_female') }}
          </label>
        </fieldset>

        <fieldset class="form-group dates">

          {{-- Don't know the birthdate --}}
          <div class="form-check" for="is_birthdate_approximate_unknown">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="is_birthdate_approximate" id="is_birthdate_approximate_unknown" value="unknown"
              @if(! in_array(old('is_birthdate_approximate'), ['approximate', 'exact']) || ! in_array($partner->is_birthdate_approximate, ['approximate', 'exact'])) checked @endif
              >

              <div class="form-inline">
                {{ trans('people.significant_other_add_unknown') }}
              </div>
            </label>
          </div>

          {{-- Approximate birthdate --}}
          <div class="form-check">
            <label class="form-check-label" for="is_birthdate_approximate_approximate">
              <input type="radio" class="form-check-input" name="is_birthdate_approximate" id="is_birthdate_approximate_approximate" value="approximate"
              @if(old('is_birthdate_approximate') === 'approximate' || $partner->is_birthdate_approximate === 'approximate') checked @endif
              >

              <div class="form-inline">
                {{ trans('people.significant_other_add_probably') }}

                <input type="number" class="form-control" id="age" name="age" value="{{ old('age') ?? $partner->age ?? 1 }}" min="1" max="99">

                {{ trans('people.significant_other_add_probably_yo') }}
              </div>
            </label>
          </div>

          {{-- Exact birthdate --}}
          <div class="form-check">
            <label class="form-check-label" for="is_birthdate_approximate_exact">
              <input type="radio" class="form-check-input" name="is_birthdate_approximate" id="is_birthdate_approximate_exact" value="exact"
              @if(old('is_birthdate_approximate') === 'exact' || $partner->is_birthdate_approximate === 'exact') checked @endif
              >

              <span class="form-inline">
                {{ trans('people.significant_other_add_exact') }}
                <input type="date" name="birthdate" class="form-control" id="specificDate"
                value="{{ old('birthdate') ?? (! is_null($partner->birthdate) ? $partner->birthdate->format('Y-m-d') : \Carbon\Carbon::now(auth()->user()->timezone)->format('Y-m-d')) ?? '' }}"
                min="{{ \Carbon\Carbon::now(Auth::user()->timezone)->subYears(120)->format('Y-m-d') }}"
                max="{{ \Carbon\Carbon::now(Auth::user()->timezone)->format('Y-m-d') }}">
              </span>
            </label>
          </div>
        </fieldset>

        <div class="classname">
          <p>{{ trans('people.significant_other_add_help') }}</p>
        </div>

        <div class="form-group actions">
          <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
          <a href="/people/{{ $contact->id }}" class="btn btn-secondary">{{ trans('app.cancel') }}</a>
        </div>
      </form>
    </div>
    <div class="tab-pane" id="existing" role="tabpanel">

      @if (count($partners) == 0)

        <div class="significant-other-blank-state">
          <img src="/img/people/no_record_found.svg">
          <p>You don’t have any contacts who can be Roger’s significant others at the moment.</p>
        </div>

      @else

        <form method="POST" action="{{ $actionExisting }}">
          {{ method_field($method) }}
          {{ csrf_field() }}

          @include('partials.errors')

          <div class="form-group">
            <label for="existingPartner">Select an existing contact as the significant other for {{ $contact->getFirstName() }}</label>
            <select class="form-control" name="existingPartner" id="existingPartner">
              @foreach ($partners as $partner)

                <option value="{{ $partner->id }}">{{ $partner->getCompleteName() }}</option>

              @endforeach
            </select>
          </div>

          <div class="form-group actions">
            <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            <a href="/people/{{ $contact->id }}" class="btn btn-secondary">{{ trans('app.cancel') }}</a>
          </div>
        </form>

      @endif
    </div>
  </div>
