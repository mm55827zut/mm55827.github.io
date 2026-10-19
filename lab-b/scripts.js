class Todo {
  constructor() {
    this.list = document.querySelector("#todo-list");
    this.form = document.querySelector("#todo-form");
    this.input = document.querySelector("#todo-input");
    this.deadline = document.querySelector("#todo-koniec");
    this.search = document.querySelector("#search-input");
    this.tasks = this.load();
    this.editingIndex = null;

    this.search.addEventListener("input", () => this.draw());

    this.form.addEventListener("submit", (event) => {
      event.preventDefault();

      try {
        this.add(this.input.value, this.deadline.value);
        this.form.reset();
        this.input.setCustomValidity("");
        this.deadline.setCustomValidity("");
      } catch (error) {
        const field = error.message.startsWith("Termin")
          ? this.deadline
          : this.input;

        field.setCustomValidity(error.message);
        field.reportValidity();
      }
    });

    this.input.addEventListener("input", () => {
      this.input.setCustomValidity("");
    });

    this.deadline.addEventListener("input", () => {
      this.deadline.setCustomValidity("");
    });

    this.list.addEventListener("click", (event) => {
      if (!(event.target instanceof Element)) return;

      const item = event.target.closest("li");
      if (!item) return;

      const index = Number(item.dataset.index);

      if (event.target.closest("button")) {
        this.remove(index);
        return;
      }

      if (this.editingIndex === index) return;
      if (this.editingIndex !== null && !this.saveEdit()) return;

      this.editingIndex = index;
      this.draw();

      const editInput = this.list.querySelector(".edit-task");
      if (editInput) editInput.focus();
    });

    document.addEventListener("click", (event) => {
      if (this.editingIndex !== null && !this.list.contains(event.target)) {
        this.saveEdit();
      }
    }, true);

    this.draw();
  }

  validate(text, deadline) {
    const taskText = text.trim();

    if (taskText.length < 3 || taskText.length > 255) {
      throw new Error("Zadanie musi mieć od 3 do 255 znaków.");
    }

    if (deadline && Date.parse(deadline) <= Date.now()) {
      throw new Error("Termin musi być pusty albo przypadać w przyszłości.");
    }

    return taskText;
  }

  add(text, deadline) {
    this.tasks.push({
      text: this.validate(text, deadline),
      deadline: deadline
    });

    this.save();
    this.draw();
  }

  remove(index) {
    this.tasks.splice(index, 1);
    this.editingIndex = null;
    this.save();
    this.draw();
  }

  edit(index, text, deadline) {
    this.tasks[index] = {
      text: this.validate(text, deadline),
      deadline: deadline
    };

    this.editingIndex = null;
    this.save();
    this.draw();
  }

  saveEdit() {
    const index = this.editingIndex;
    if (index === null) return true;

    const text = this.list.querySelector(".edit-task");
    const deadline = this.list.querySelector(".edit-deadline");

    try {
      this.edit(index, text.value, deadline.value);
      return true;
    } catch (error) {
      const field = error.message.startsWith("Termin") ? deadline : text;
      field.setCustomValidity(error.message);
      field.reportValidity();
      return false;
    }
  }

  addHighlightedText(container, text, searchText) {
    if (searchText.length < 2) {
      container.textContent = text;
      return;
    }

    const lowerText = text.toLowerCase();
    let start = 0;
    let match = lowerText.indexOf(searchText);

    while (match !== -1) {
      container.append(document.createTextNode(text.slice(start, match)));

      const mark = document.createElement("mark");
      mark.textContent = text.slice(match, match + searchText.length);
      container.append(mark);

      start = match + searchText.length;
      match = lowerText.indexOf(searchText, start);
    }

    container.append(document.createTextNode(text.slice(start)));
  }

  draw() {
    this.list.replaceChildren();

    const searchText = this.search.value.trim().toLowerCase();

    this.tasks.forEach((task, index) => {
      if (
        searchText.length >= 2 &&
        !task.text.toLowerCase().includes(searchText)
      ) {
        return;
      }

      const item = document.createElement("li");
      item.dataset.index = index;

      if (this.editingIndex === index) {
        const textInput = document.createElement("input");
        textInput.className = "edit-task";
        textInput.type = "text";
        textInput.value = task.text;
        textInput.setAttribute("aria-label", "Treść zadania");

        const dateInput = document.createElement("input");
        dateInput.className = "edit-deadline";
        dateInput.type = "datetime-local";
        dateInput.value = task.deadline;
        dateInput.setAttribute("aria-label", "Termin");

        item.append(textInput, dateInput);
      } else {
        const text = document.createElement("span");
        this.addHighlightedText(text, task.text, searchText);

        const date = document.createElement("span");
        date.className = "todo-date";
        date.textContent = task.deadline.replace("T", " ");
        date.setAttribute("aria-label", "Termin");

        item.append(text, date);
      }

      const removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.textContent = "Usuń";
      removeButton.setAttribute("aria-label", "Usuń zadanie");

      item.append(removeButton);
      this.list.append(item);
    });
  }

  load() {
    const savedTasks = localStorage.getItem("todo-tasks");

    if (savedTasks === null) return [];

    const tasks = JSON.parse(savedTasks);

    if (
      !Array.isArray(tasks) ||
      !tasks.every(
        (task) =>
          task &&
          typeof task.text === "string" &&
          typeof task.deadline === "string"
      )
    ) {
      throw new Error();
    }

    return tasks;
  }

  save() {
    localStorage.setItem("todo-tasks", JSON.stringify(this.tasks));
  }
}

document.todo = new Todo();
