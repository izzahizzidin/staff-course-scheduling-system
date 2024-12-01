var cal = {

  // (A) PROPERTIES
  mon: false, // monday first
  events: null, // events data for current month/year
  sMth: 0,
  sYear: 0, // selected month & year
  hMth: null,
  hYear: null, // html month & year
  hCD: null,
  hCB: null, // html calendar days & body
  // html form & fields
  hFormWrap: null,
  hForm: null,
  hfID: null,
  hfStart: null,
  hfEnd: null,
  hfTxt: null,
  hfColor: null,
  hfBG: null,
  hfParticipants: null,
  hfCourseCategory: null,
  hfDivision: null,
  hfVenue: null,
  hfDuration: null,
  hfCategoryArea: null,
  hfDel: null,

  // (B) SUPPORT FUNCTION - AJAX FETCH
  ajax: (data, onload) => {
    // (B1) FORM DATA
    let form = new FormData();
    for (let [k, v] of Object.entries(data)) {
      form.append(k, v);
    }

    // (B2) FETCH
    fetch("3-cal-ajax-readonly.php", {
        method: "POST",
        body: form
      })
      .then(res => res.text())
      .then(txt => onload(txt))
      .catch(err => console.error(err));
  },

  // (C) INIT CALENDAR
  init: () => {
    // (C1) GET HTML ELEMENTS
    cal.hMth = document.getElementById("calMonth");
    cal.hYear = document.getElementById("calYear");
    cal.hCD = document.getElementById("calDays");
    cal.hCB = document.getElementById("calBody");
    cal.hFormWrap = document.getElementById("calForm");
    cal.hForm = cal.hFormWrap.querySelector("form");
    cal.hfID = document.getElementById("evtID");
    cal.hfStart = document.getElementById("evtStart");
    cal.hfEnd = document.getElementById("evtEnd");
    cal.hfTxt = document.getElementById("evtTxt");
    cal.hfColor = document.getElementById("evtColor");
    cal.hfBG = document.getElementById("evtBG");
    cal.hfParticipants = document.getElementById("evtParticipants");
    cal.hfCourseCategory = document.getElementById("evtCourseCategory");
    cal.hfDivision = document.getElementById("evtDivision");
    cal.hfVenue = document.getElementById("evtVenue");
    cal.hfDuration = document.getElementById("evtDuration");
    cal.hfCategoryArea = document.getElementById("evtCategoryArea");
    cal.hfDel = document.getElementById("evtDel");

    // (C2) ATTACH CONTROLS
    cal.hMth.onchange = cal.load;
    cal.hYear.onchange = cal.load;
    document.getElementById("calBack").onclick = () => cal.pshift();
    document.getElementById("calNext").onclick = () => cal.pshift(1);
    document.getElementById("evtCX").onclick = () => cal.hFormWrap.close();

    // (C3) DRAW DAY NAMES
    let days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    if (cal.mon) {
      days.push("Sun");
    } else {
      days.unshift("Sun");
    }
    for (let d of days) {
      let cell = document.createElement("div");
      cell.className = "calCell";
      cell.innerHTML = d;
      cal.hCD.appendChild(cell);
    }

    // (C4) LOAD & DRAW CALENDAR
    cal.load();
  },

  // (D) SHIFT CURRENT PERIOD BY 1 MONTH
  pshift: forward => {
    cal.sMth = parseInt(cal.hMth.value, 10);
    cal.sYear = parseInt(cal.hYear.value, 10);
    if (forward) {
      cal.sMth++;
    } else {
      cal.sMth--;
    }
    if (cal.sMth > 12) {
      cal.sMth = 1;
      cal.sYear++;
    }
    if (cal.sMth < 1) {
      cal.sMth = 12;
      cal.sYear--;
    }
    cal.hMth.value = cal.sMth;
    cal.hYear.value = cal.sYear;
    cal.load();
  },

  // (E) LOAD EVENTS
  load: () => {
    cal.sMth = parseInt(cal.hMth.value);
    cal.sYear = parseInt(cal.hYear.value);
    cal.ajax({
      req: "get",
      month: cal.sMth,
      year: cal.sYear
    }, events => {
      cal.events = JSON.parse(events);
      cal.draw();
    });
  },

  // (F) DRAW CALENDAR
  draw: () => {
    // (F1) CALCULATE DAY MONTH YEAR
    // note - jan is 0 & dec is 11 in js
    // note - sun is 0 & sat is 6 in js
    let daysInMth = new Date(cal.sYear, cal.sMth, 0).getDate(), // number of days in selected month
      startDay = new Date(cal.sYear, cal.sMth - 1, 1).getDay(), // first day of the month
      endDay = new Date(cal.sYear, cal.sMth - 1, daysInMth).getDay(), // last day of the month
      now = new Date(), // current date
      nowMth = now.getMonth() + 1, // current month
      nowYear = parseInt(now.getFullYear()), // current year
      nowDay = cal.sMth == nowMth && cal.sYear == nowYear ? now.getDate() : null;

    // (F2) DRAW CALENDAR ROWS & CELLS
    // (F2-1) INIT + HELPER FUNCTIONS
    let rowA, rowB, rowC, rowMap = {},
      rowNum = 1,
      cell, cellNum = 1,
      rower = () => {
        rowA = document.createElement("div");
        rowB = document.createElement("div");
        rowC = document.createElement("div");
        rowA.className = "calRow";
        rowA.id = "calRow" + rowNum;
        rowB.className = "calRowHead";
        rowC.className = "calRowBack";
        cal.hCB.appendChild(rowA);
        rowA.appendChild(rowB);
        rowA.appendChild(rowC);
      },
      celler = day => {
        cell = document.createElement("div");
        cell.className = "calCell";
        if (day) {
          cell.innerHTML = day;
        }
        rowB.appendChild(cell);
        cell = document.createElement("div");
        cell.className = "calCell";
        if (day === undefined) {
          cell.classList.add("calBlank");
        }
        if (day !== undefined && day == nowDay) {
          cell.classList.add("calToday");
        }
        rowC.appendChild(cell);
      };
    cal.hCB.innerHTML = "";
    rower();

    // (F2-2) BLANK CELLS BEFORE START OF MONTH
    if (cal.mon && startDay != 1) {
      let blanks = startDay == 0 ? 7 : startDay;
      for (let i = 1; i < blanks; i++) {
        celler();
        cellNum++;
      }
    }
    if (!cal.mon && startDay != 0) {
      for (let i = 0; i < startDay; i++) {
        celler();
        cellNum++;
      }
    }

    // (F2-3) DAYS OF THE MONTH
    for (let i = 1; i <= daysInMth; i++) {
      rowMap[i] = {
        r: rowNum,
        c: cellNum
      };
      celler(i);
      if (i != daysInMth && cellNum % 7 == 0) {
        rowNum++;
        rower();
      }
      cellNum++;
    }

    // (F2-4) BLANK CELLS AFTER END OF MONTH
    if (cal.mon && endDay != 0) {
      let blanks = endDay == 6 ? 1 : 7 - endDay;
      for (let i = 0; i < blanks; i++) {
        celler();
        cellNum++;
      }
    }
    if (!cal.mon && endDay != 6) {
      let blanks = endDay == 0 ? 6 : 6 - endDay;
      for (let i = 0; i < blanks; i++) {
        celler();
        cellNum++;
      }
    }

    // (F3) DRAW EVENTS
    if (cal.events !== false) {
      for (let [id, evt] of Object.entries(cal.events)) {
        // (F3-1) EVENT START & END DAY
        let sd = new Date(evt.s),
          ed = new Date(evt.e);
        if (sd.getFullYear() != cal.sYear) {
          sd = 1;
        } else {
          sd = sd.getMonth() + 1 < cal.sMth ? 1 : sd.getDate();
        }
        if (ed.getFullYear() != cal.sYear) {
          ed = daysInMth;
        } else {
          ed = ed.getMonth() + 1 > cal.sMth ? daysInMth : ed.getDate();
        }

        // (F3-2) "MAP" ONTO HTML CALENDAR
        cell = {};
        rowNum = 0;
        for (let i = sd; i <= ed; i++) {
          if (rowNum != rowMap[i]["r"]) {
            cell[rowMap[i]["r"]] = {
              s: rowMap[i]["c"],
              e: 0
            };
            rowNum = rowMap[i]["r"];
          }
          if (cell[rowNum]) {
            cell[rowNum]["e"] = rowMap[i]["c"];
          }
        }

        // (F3-3) DRAW HTML EVENT ROW
        for (let [r, c] of Object.entries(cell)) {
          let o = c.s - 1 - ((r - 1) * 7), // event cell offset
            w = c.e - c.s + 1; // event cell width
          rowA = document.getElementById("calRow" + r);
          rowB = document.createElement("div");
          rowB.className = "calRowEvt";
          rowB.innerHTML = cal.events[id]["t"];
          rowB.style.color = cal.events[id]["c"];
          rowB.style.backgroundColor = cal.events[id]["b"];
          rowB.classList.add("w" + w);
          if (o != 0) {
            rowB.classList.add("o" + o);
          }
          rowB.onclick = () => cal.show(id);
          rowA.appendChild(rowB);
        }
      }
    }
  },

  // (G) SHOW EVENT FORM
  show: id => {
    if (id) {
      cal.hfID.value = id;
      cal.hfStart.value = cal.events[id]["s"].replace(" ", "T").substring(0, 16);
      cal.hfEnd.value = cal.events[id]["e"].replace(" ", "T").substring(0, 16);
      cal.hfTxt.value = cal.events[id]["t"];
      cal.hfColor.value = cal.events[id]["c"];
      cal.hfBG.value = cal.events[id]["b"];
      cal.hfParticipants.value = cal.events[id]["participants"].join(", ");
      cal.hfCourseCategory.value = cal.events[id]["cc"];
      cal.hfDivision.value = cal.events[id]["d"];
      cal.hfVenue.value = cal.events[id]["v"];
      cal.hfDuration.value = cal.events[id]["dur"];
      cal.hfCategoryArea.value = cal.events[id]["ca"];
      cal.hfDel.style.display = "none";
    } else {
      cal.hForm.reset();
      cal.hfID.value = "";
      cal.hfDel.style.display = "none";
    }
    cal.hFormWrap.show();
  },

  // (H) VIEW EVENT
  view: () => {
    // (H1) COLLECT DATA
    var data = {
      req: "view",
      id: cal.hfID.value
    };
    console.log("Data to be sent: ", data); // Debugging line

    // (H2) AJAX FETCH
    cal.ajax(data, (txt) => {
      // (H3) PARSE RESPONSE AND SHOW FORM
      var res = JSON.parse(txt);
      if (res.status) {
        // Check if the Pers_No of the logged in user is in evt_participants
        var participants = res.participants;
        var isUserParticipant = participants.includes(loggedInUser);

        // Set the value of the Participants field based on whether the logged in user is a participant or not
        if (isUserParticipant) {
          cal.hfParticipants.value = participants.join(", ");
        } else {
          cal.hfParticipants.value = "You are not a participant in this event.";
        }

        cal.hfStart.value = res.start;
        cal.hfEnd.value = res.end;
        cal.hfTxt.value = res.txt;
        cal.hfColor.value = res.color;
        cal.hfBG.value = res.bg;
        cal.hfCourseCategory.value = res.coursecategory;
        cal.hfDivision.value = res.division;
        cal.hfVenue.value = res.venue;
        cal.hfDuration.value = res.duration;
        cal.hfCategoryArea.value = res.categoryarea;

        cal.hFormWrap.showModal();
        cal.hfDel.style.display = "block";
        cal.hfDel.disabled = false;
      } else {
        alert(res.message);
      }
    });
  },

};
window.onload = cal.init;